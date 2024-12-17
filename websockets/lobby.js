exports.createLobby = () => {
  const games = new Map();
  let id = 1;

  const addGame = (user, socketId, board, gameID) => {
    id++
    const game = {
      id: id,
      total_players: 2,
      players: 1,
      created_at: Date.now(),
      board: board,
      game_id: gameID,
    };
    game[`player${game.players}`] = user;
    game[`player${game.players}SocketId`] = socketId;
    games.set(id, game);
    return game;
  };

  const addPlayerToGame = (game, user, socketId) => {
    game.players++;
    game[`player${game.players}`] = user;
    game[`player${game.players}SocketId`] = socketId;
    return game;
  };

  const removeGame = (id) => {
    games.delete(id);
    return games;
  };

  const existsGame = (id) => {
    return games.has(id);
  };

  const getGame = (id) => {
    return games.get(id);
  };

  const getGames = () => {
    return [...games.values()];
  };

  const leaveLobby = (socketId) => {
    const gamesToDelete = [...games.values()].filter(
      (game) => game.player1SocketId == socketId
    );
    gamesToDelete.forEach((game) => {
      games.delete(game.id);
    });
    return getGames();
  };

  return {
    getGames,
    getGame,
    addGame,
    removeGame,
    existsGame,
    leaveLobby,
    addPlayerToGame,
  };
};
