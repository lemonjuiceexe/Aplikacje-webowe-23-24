import { Injectable } from '@angular/core';
import {Player} from "../components/lobby/lobby.component";

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
}
