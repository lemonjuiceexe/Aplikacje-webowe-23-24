import { Injectable } from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class LobbyService {

  constructor() { }
  joinLobby(playerName: string) {
    console.log("Joining lobby as " + playerName);
    return fetch(`http://127.0.0.1/chinese/server/join_lobby.php?playerName=${playerName}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "Access-Control-Allow-Origin": "localhost"
      }
    });
  }
}
