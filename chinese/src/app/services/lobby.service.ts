import {Injectable} from '@angular/core';
import {Lobby, Player} from "../components/lobby/lobby.component";

@Injectable({
  providedIn: 'root'
})
export class LobbyService {

  constructor() {
  }
  getWords(language: string) {
    return fetch(`http://localhost/server/get_words.php?language=${language}`, {
      method: "GET",
      headers: {
        "Access-Control-Allow-Origin": "*",
        "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Credentials",
      }
    });
  }

  joinLobby(playerName: string) {
    return fetch(`http://localhost/server/join_lobby.php?playerName=${playerName}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "Access-Control-Allow-Origin": "*",
        "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Credentials",
      }
    });
  }
  toggleReady(player: Player, lobby: Lobby) {
    const formData = new FormData();
    formData.append("playerSecret", player.secret);
    formData.append("lobbyId", lobby.id.toString());
    return fetch(`http://localhost/server/toggle_ready.php`, {
      method: "POST",
      headers: {
        "Access-Control-Allow-Origin": "*",
        "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Credentials",
      },
      body: formData
    });
  }

  getLobbyState(lobbyId: number) {
    return fetch(`http://localhost/server/get_lobby_state.php?lobbyId=${lobbyId}`, {
      method: "GET",
      headers: {
        "Access-Control-Allow-Origin": "*",
        "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Credentials",
      }
    });
  }
}
