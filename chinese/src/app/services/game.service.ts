import { Injectable } from '@angular/core';
import {Player} from "../components/lobby/lobby.component";
import {Pawn} from "../components/board/board/board.component";

@Injectable({
  providedIn: 'root'
})
export class GameService {

  constructor() { }

  rollDice(player: Player, lobbyId: number) {
    const formData = new FormData();
    formData.append("playerSecret", player.secret);
    formData.append("lobbyId", lobbyId.toString());
    return fetch("http://127.0.0.1/chinese/server/dice_roll.php", {
      method: "POST",
      headers: {
        "Access-Control-Allow-Origin": "http://localhost",
        "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Credentials",
      },
      body: formData
    });
  }

  movePawn(player: Player, lobbyId: number, pawn: Pawn) {
    console.log("Moving pawn color: ", pawn.color, " cellsTraveled: ", pawn.cellsTraveled);
    const formData = new FormData();
    formData.append("playerSecret", player.secret);
    formData.append("lobbyId", lobbyId.toString());
    formData.append("cellsTraveled", pawn.cellsTraveled.toString());
    return fetch("http://127.0.0.1/chinese/server/move_pawn.php", {
      method: "POST",
      headers: {
        "Access-Control-Allow-Origin": "http://localhost",
        "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Credentials",
      },
      body: formData
    });
  }
}
