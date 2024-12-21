exports.createGameEngine = () => {
  const initGame = (gameFromDB) => {
    gameFromDB.gameStatus = null;
    // null -> game has not started yet
    // 1 -> player 1 is the winner
    // 2 -> player 2 is the winner
    // 3 -> game has ended in a draw
    gameFromDB.currentPlayer = 1;
    gameFromDB.pairsFound = 0;
    return gameFromDB;
  };

  // ------------------------------------------------------
  // Actions / Methods
  // ------------------------------------------------------

  // Check if the board is complete and change the gameStatus accordingly
  const changeGameStatus = (game) => {
    if (game.pairsFound === game.board.columns * game.board.rows) {
      if (game.player1_pairs > game.player2_pairs) {
        game.gameStatus = 1; // player 1 wins
      }
      if (game.player2_pairs > game.player1_pairs) {
        game.gameStatus = 2; // player 2 wins
      }
      if (game.player1_pairs === game.player2_pairs) {
        game.player1_turns > game.player2_turns
          ? (game.gameStatus = 2)
          : (game.gameStatus = 1);
      }
    }
  };

  // returns whether the game as ended or not
  const gameEnded = (game) => game.gameStatus > 0;

  const play = (game, card, playerSocketId) => {
    if (
      playerSocketId != game.player1SocketId &&
      playerSocketId != game.player2SocketId
    ) {
      return {
        errorCode: 10,
        errorMessage: "You are not playing this game!",
      };
    }
    if (gameEnded(game)) {
      return {
        errorCode: 11,
        errorMessage: "Game has already ended!",
      };
    }
    if (
      (game.currentPlayer == 1 && playerSocketId != game.player1SocketId) ||
      (game.currentPlayer == 2 && playerSocketId != game.player2SocketId)
    ) {
      return {
        errorCode: 12,
        errorMessage: "Invalid play: It is not your turn!",
      };
    }
    game.cards[card.uniqueID] = card;
    game.currentPlayer === 1 ? game.player1_turns++ : game.player2_turns++;
    if (card.matched) {
      game.pairsFound++;
      if (game.currentPlayer === 1) {
        game.player1_pairs++;
      } else {
        game.player2_pairs++;
      }
    }
    changeGameStatus(game);
    return true;
  };

  // One of the players quits the game. The other one wins the game
  const quit = (game, playerSocketId) => {
    if (
      playerSocketId != game.player1SocketId &&
      playerSocketId != game.player2SocketId
    ) {
      return {
        errorCode: 10,
        errorMessage: "You are not playing this game!",
      };
    }
    if (gameEnded(game)) {
      return {
        errorCode: 11,
        errorMessage: "Game has already ended!",
      };
    }
    game.gameStatus = playerSocketId == game.player1SocketId ? 2 : 1;
    return true;
  };

  // Check if socket can close the game (game must have ended and player must belong to game)
  const close = (game, playerSocketId) => {
    if (
      playerSocketId != game.player1SocketId &&
      playerSocketId != game.player2SocketId
    ) {
      return {
        errorCode: 10,
        errorMessage: "You are not playing this game!",
      };
    }
    if (!gameEnded(game)) {
      return {
        errorCode: 14,
        errorMessage: "Cannot close a game that has not ended!",
      };
    }
    return true;
  };

  const switchPlayer = (game) => {
    console.log("switchPlayer");
    game.currentPlayer = game.currentPlayer === 1 ? 2 : 1;
    return game;
  };

  return {
    initGame,
    gameEnded,
    play,
    quit,
    close,
    switchPlayer,
  };
};
