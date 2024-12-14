exports.createGameEngine = () => {
    const initGame = (gameFromDB) => {
        gameFromDB.gameStatus = null
        // null -> game has not started yet 
        // 0 -> game has started and running
        // 1 -> player 1 is the winner
        // 2 -> player 2 is the winner
        // 3 -> draw
        gameFromDB.currentPlayer = 1
        gameFromDB.board = [0, 0, 0, 0, 0, 0, 0, 0, 0]
        return gameFromDB
    }

    // ------------------------------------------------------
    // Actions / Methods
    // ------------------------------------------------------

    // Check if there is a line (either horizontal, vertical or diagonal) with the same player
    const hasFullLine = (game, playerNumber) =>
        ((game.board[0] === playerNumber) && (game.board[1] === playerNumber) && (game.board[2] === playerNumber)) ||
        ((game.board[3] === playerNumber) && (game.board[4] === playerNumber) && (game.board[5] === playerNumber)) ||
        ((game.board[6] === playerNumber) && (game.board[7] === playerNumber) && (game.board[8] === playerNumber)) ||
        ((game.board[0] === playerNumber) && (game.board[3] === playerNumber) && (game.board[6] === playerNumber)) ||
        ((game.board[1] === playerNumber) && (game.board[4] === playerNumber) && (game.board[7] === playerNumber)) ||
        ((game.board[2] === playerNumber) && (game.board[5] === playerNumber) && (game.board[8] === playerNumber)) ||
        ((game.board[0] === playerNumber) && (game.board[4] === playerNumber) && (game.board[8] === playerNumber)) ||
        ((game.board[2] === playerNumber) && (game.board[4] === playerNumber) && (game.board[6] === playerNumber))


    // Check if the board is complete and change the gameStatus accordingly
    const changeGameStatus = (game) => {
        if (hasFullLine(game, 1)) {
            game.gameStatus = 1
        } else if (hasFullLine(game, 2)) {
            game.gameStatus = 2
        } else if (isBoardComplete(game)) {
            game.gameStatus = 3
        } else {
            game.gameStatus = 0
        }
    }
    
    // Check if the board is complete (no further plays are possible)
    const isBoardComplete = (game) => game.board.findIndex(piece => piece === 0) < 0

    // returns whether the game as ended or not
    const gameEnded = (game) => game.gameStatus > 0


    // Plays a specific piece of the game (defined by its index)
    // Returns true if the game play is valid or an object with an error code and message otherwise
    const play = (game, index, playerSocketId) => {
        if ((playerSocketId != game.player1SocketId) && (playerSocketId != game.player2SocketId)){
            return {
                errorCode: 10,
                errorMessage: 'You are not playing this game!'
            }
        }
        if (gameEnded(game)) {
            return {
                errorCode: 11,
                errorMessage: 'Game has already ended!'
            }
        }
        if (((game.currentPlayer == 1) && (playerSocketId != game.player1SocketId))
            ||
            ((game.currentPlayer == 2) && (playerSocketId != game.player2SocketId))){
            return {
                errorCode: 12,
                errorMessage: 'Invalid play: It is not your turn!'
            }
        }
        if (game.board[index] !== 0) {
            return {
                errorCode: 13,
                errorMessage: 'Invalid play: cell already occupied!'
            }
        }
        game.board[index] = game.currentPlayer
        game.currentPlayer = game.currentPlayer === 1 ? 2 : 1
        changeGameStatus(game)
        return true
    }

    // One of the players quits the game. The other one wins the game
    const quit = (game, playerSocketId) => {
        if ((playerSocketId != game.player1SocketId) && (playerSocketId != game.player2SocketId)){
            return {
                errorCode: 10,
                errorMessage: 'You are not playing this game!'
            }
        }
        if (gameEnded(game)) {
            return {
                errorCode: 11,
                errorMessage: 'Game has already ended!'
            }
        }
        game.gameStatus = playerSocketId == game.player1SocketId ? 2 : 1
        game.status = 'ended'
        return true
    }

    // Check if socket can close the game (game must have ended and player must belong to game)
    const close = (game, playerSocketId) => {
        if ((playerSocketId != game.player1SocketId) && (playerSocketId != game.player2SocketId)){
            return {
                errorCode: 10,
                errorMessage: 'You are not playing this game!'
            }
        }
        if (!gameEnded(game)) {
            return {
                errorCode: 14,
                errorMessage: 'Cannot close a game that has not ended!'
            }
        }
        return true
    }
    
    return {
        initGame,
        gameEnded,
        play,
        quit,
        close
    }
}