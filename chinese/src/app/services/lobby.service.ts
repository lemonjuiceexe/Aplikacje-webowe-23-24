import {Injectable} from '@angular/core';
import {Lobby, Player} from "../components/lobby/lobby.component";

@Injectable({
  providedIn: 'root'
})
export class LobbyService {

  constructor() {
  }
  joinLobby(playerName: string) {
    return fetch(`http://127.0.0.1/chinese/server/join_lobby.php?playerName=${playerName}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "Access-Control-Allow-Origin": "http://localhost",
        "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Credentials",
      }
    });
  }
  toggleReady(player: Player, lobby: Lobby) {
    const formData = new FormData();
    formData.append("playerSecret", player.secret);
    formData.append("lobbyId", lobby.id.toString());
    return fetch(`http://127.0.0.1/chinese/server/toggle_ready.php`, {
      method: "POST",
      headers: {
        "Access-Control-Allow-Origin": "http://localhost",
        "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Credentials",
      },
      body: formData
    });
  }
}
