import {Component } from '@angular/core';
import {Color} from "../board/board/board.component";
import {FormsModule} from "@angular/forms";
import {NgIf} from "@angular/common";

interface Player{
  name: string;
  ready: boolean;
  color: Color;
}
interface Lobby{
  id: number;
  players: Player[];
  gameState: Object;
}
interface JoinLobbyResponse {
  lobby: Lobby;
  player: Player;
}

@Component({
  selector: 'app-lobby',
  standalone: true,
  imports: [
    FormsModule,
    NgIf
  ],
  templateUrl: './lobby.component.html',
  styleUrl: './lobby.component.css'
})
export class LobbyComponent {
  lobby: Lobby | null = null;
  player: Player = {
    name: "",
    ready: false,
    color: Color.Red
  };

  constructor() { }
  joinLobby(e: Event) {
    e.preventDefault();
    e.stopPropagation();
    console.log("Joining lobby !!!!!");
    fetch(`http://127.0.0.1/chinese/server/join_lobby.php?playerName=${this.player.name}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json",
        "Access-Control-Allow-Origin": "http://localhost",
        "Access-Control-Allow-Headers": "Content-Type, Access-Control-Allow-Origin, Access-Control-Allow-Headers, Access-Control-Allow-Credentials",
      },
      credentials: "include"
    })
      .then(response => response.json())
      .then((response: JoinLobbyResponse) => {
        this.lobby = response.lobby;
        this.player = response.player;

        console.log(JSON.stringify(this.lobby));
        console.log("im " + this.player);
      });
      // .then(text => text.text())
      // .then(text => console.log(text));
  }
}
