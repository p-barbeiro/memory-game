const { createLobby } = require("./lobby");
const lobby = createLobby();
const { createUtil } = require("./util");
const util = createUtil();
const { createGameEngine } = require("./gameEngine");
const gameEngine = createGameEngine();

gameEngine.initGame({ id: 1, player1: { id: 1, name: "Player 1" } });

const httpServer = require("http").createServer();
const io = require("socket.io")(httpServer, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"],
    credentials: true,
  },
});

const PORT = process.env.APP_PORT || 8086;

httpServer.listen(PORT, () => {
  console.log(`listening on localhost:${PORT}`);
});

io.on("connection", (socket) => {
  console.log(`Client with socket id ${socket.id} has connected!`);

  // ------------------------------------------------------
  // Disconnect
  // ------------------------------------------------------
  // disconnection event is triggered when the client disconnects but is still on the rooms

  socket.on("disconnecting", (reason) => {
    socket.rooms.forEach((room) => {
      if (room == "lobby") {
        lobby.leaveLobby(socket.id);
        io.to("lobby").emit("lobbyChanged", lobby.getGames());
      }
    });
    util.getRoomGamesPlaying(socket).forEach(([roomName, room]) => {
      socket.leave(roomName);
      if (!gameEngine.gameEnded(room.game)) {
        room.game.status = "interrupted";
        room.game.gameStatus = 3;
        io.to(roomName).emit("gameInterrupted", room.game);
      }
    });
  });

  // ------------------------------------------------------
  // User identity
  // ------------------------------------------------------

  socket.on("login", (user) => {
    // Stores user information on the socket as "user" property
    socket.data.user = user;
    if (user && user.id) {
      socket.join("user_" + user.id);
      socket.join("lobby");
    }
  });

  socket.on("logout", (user) => {
    if (user && user.id) {
      socket.leave("user_" + user.id);
      lobby.leaveLobby(socket.id);
      io.to("lobby").emit("lobbyChanged", lobby.getGames());
      socket.leave("lobby");
      util.getRoomGamesPlaying(socket).forEach(([roomName, room]) => {
        socket.leave(roomName);
        if (!gameEngine.gameEnded(room.game)) {
          room.game.status = "interrupted";
          room.game.gameStatus = 3;
          io.to(roomName).emit("gameInterrupted", room.game);
        }
      });
    }
    socket.data.user = undefined;
  });

  // ------------------------------------------------------
  // Chat and Private Messages
  // ------------------------------------------------------

  socket.on("chatMessage", (message) => {
    const payload = {
      user: socket.data.user,
      message: message,
    };
    io.sockets.emit("chatMessage", payload);
  });

  // ------------------------------------------------------
  // Lobby
  // ------------------------------------------------------

  socket.on("fetchGames", (callback) => {
    if (!util.checkAuthenticatedUser(socket, callback)) {
      return;
    }
    const games = lobby.getGames();
    if (callback) {
      callback(games);
    }
  });

  socket.on("addGame", (board, gameID, callback) => {
    if (!util.checkAuthenticatedUser(socket, callback)) {
      return;
    }

    const game = lobby.addGame(socket.data.user, socket.id, board, gameID);
    io.to("lobby").emit("lobbyChanged", lobby.getGames());
    console.log("User " + socket.data.user.name + " is creating a game");
    console.log("Game created: ", game);

    if (callback) {
      callback(game);
    }
  });

  socket.on("joinGame", (id, callback) => {
    if (!util.checkAuthenticatedUser(socket, callback)) {
      return;
    }
    const game = lobby.getGame(id);
    if (socket.data.user.id == game.player1.id) {
      if (callback) {
        callback({
          errorCode: 3,
          errorMessage: "User cannot join a game that he created!",
        });
      }
      return;
    }
    lobby.addPlayerToGame(game, socket.data.user, socket.id);
    if (game.players == game.total_players) {
      lobby.removeGame(id);
      io.to("lobby").emit("lobbyChanged", lobby.getGames());
      console.log(game);
      if (callback) {
        callback(game);
      }
    }
  });

  socket.on("removeGame", (id, callback) => {
    if (!util.checkAuthenticatedUser(socket, callback)) {
      return;
    }
    const game = lobby.getGame(id);
    if (socket.data.user.id != game.player1.id) {
      if (callback) {
        callback({
          errorCode: 4,
          errorMessage: "User cannot remove a game that he has not created!",
        });
      }
      return;
    }
    lobby.removeGame(game.id);
    io.to("lobby").emit("lobbyChanged", lobby.getGames());
    if (callback) {
      callback(game);
    }
  });

  // ------------------------------------------------------
  // Multiplayer Game
  // ------------------------------------------------------

  socket.on("startGame", (clientGame, callback) => {
    if (!util.checkAuthenticatedUser(socket, callback)) {
      return;
    }
    const roomName = "game_" + clientGame.id;
    const game = gameEngine.initGame(clientGame);
    // join the 2 players to the game room
    io.sockets.sockets.get(game.player1SocketId)?.join(roomName);
    io.sockets.sockets.get(game.player2SocketId)?.join(roomName);
    // store the game data directly on the room object:
    socket.adapter.rooms.get(roomName).game = game;
    console.log(socket.adapter.rooms.get(roomName));

    // emit the "gameStarted" to all users in the room
    io.to(roomName).emit("gameStart", game);
    if (callback) {
      callback(game);
    }
  });

  socket.on("fetchPlayingGames", (callback) => {
    if (!util.checkAuthenticatedUser(socket, callback)) {
      return;
    }
    if (callback) {
      callback(util.getGamesPlaying(socket));
    }
  });

  socket.on("play", (playData, callback) => {
    if (!util.checkAuthenticatedUser(socket, callback)) {
      return;
    }
    const roomName = "game_" + playData.gameId;
    // load game state from the game data stored directly on the room object:
    const game = socket.adapter.rooms.get(roomName).game;
    const playResult = gameEngine.play(game, playData.card, socket.id);
    if (playResult !== true) {
      if (callback) {
        callback(playResult);
      }
      return;
    }
    // notify all users playing the game (in the room) that the game state has changed
    // Also, notify them that the game has ended
    io.to(roomName).emit("gameChanged", game);
    if (gameEngine.gameEnded(game)) {
      io.to(roomName).emit("gameEnded", game);
    }
    if (callback) {
      callback(game);
    }
  });

  socket.on("switchPlayer", (gameId, callback) => {
    if (!util.checkAuthenticatedUser(socket, callback)) {
      return;
    }
    const roomName = "game_" + gameId;
    // load game state from the game data stored directly on the room object:
    const game = socket.adapter.rooms.get(roomName).game;
    gameEngine.switchPlayer(game);
    
    // notify all users playing the game (in the room) that the game state has changed
    // Also, notify them that the game has ended
    io.to(roomName).emit("gameChanged", game);
    if (gameEngine.gameEnded(game)) {
      io.to(roomName).emit("gameEnded", game);
    }
    if (callback) {
      callback(game);
    }
  });

  socket.on("quitGame", (gameId, callback) => {
    if (!util.checkAuthenticatedUser(socket, callback)) {
      return;
    }
    const roomName = "game_" + gameId;
    // load game state from the game data stored directly on the room object:
    const game = socket.adapter.rooms.get(roomName).game;
    const quitResult = gameEngine.quit(game, socket.id);
    if (quitResult !== true) {
      if (callback) {
        callback(quitResult);
      }
      return;
    }
    // notify all users playing the game (in the room) that the game state has changed
    // Also, notify them that the game has been quit and the game has ended
    io.to(roomName).emit("gameChanged", game);
    io.to(roomName).emit("gameQuitted", {
      userQuit: socket.data.user,
      game: game,
    });
    if (gameEngine.gameEnded(game)) {
      io.to(roomName).emit("gameEnded", game);
    }
    socket.leave(roomName);
    if (callback) {
      callback(game);
    }
  });

  socket.on("closeGame", (gameId, callback) => {
    if (!util.checkAuthenticatedUser(socket, callback)) {
      return;
    }
    const roomName = "game_" + gameId;
    // load game state from the game data stored directly on the room object:
    const game = socket.adapter.rooms.get(roomName).game;
    const closeResult = gameEngine.close(game, socket.id);
    if (closeResult !== true) {
      if (callback) {
        callback(closeResult);
      }
      return;
    }
    socket.leave(roomName);
    if (callback) {
      callback(true);
    }
  });

});
