import {Component} from '@angular/core';
import {BoardComponent, Color} from "../board/board/board.component";
import {FormsModule} from "@angular/forms";
import {NgForOf, NgIf} from "@angular/common";
import { LobbyService } from "../../services/lobby.service";

export interface Player{
  name: string;
  secret: string;
  isReady: boolean;
  color: Color;
}
export interface Lobby{
  id: number;
  players: Player[];
  gameState: GameStateServer | null;
  lastWinner: Color | null;
}
export interface GameStateServer {
  redTravelled: number[];
  blueTravelled: number[];
  greenTravelled: number[];
  yellowTravelled: number[];
  currentTurn: Color;
  diceValue: number;
  roundStartTimestamp: number;
}
interface JoinLobbyResponse {
  lobby: Lobby;
  player: Player;
}
interface LobbyStateResponse {
  players: Player[];
  gameState: GameStateServer | null;
  lastWinner: Color | null;
}
@Component({
  selector: 'app-lobby',
  standalone: true,
  imports: [
    FormsModule,
    NgIf,
    NgForOf,
    BoardComponent
  ],
  templateUrl: './lobby.component.html',
  styleUrl: './lobby.component.css',
})
export class LobbyComponent {
  lobby: Lobby | null = null;
  player: Player = {
    name: "",
    secret: "",
    isReady: false,
    color: Color.Red
  };

  gameStarted: boolean = this.lobby !== null && this.lobby.gameState !== null;
  words: any = {};
  currentLanguage: string = "en_US";

  constructor(private lobbyService: LobbyService) { }

  ngOnInit() {
    if(localStorage.getItem("lobby") !== null){
      this.lobby = JSON.parse(localStorage.getItem("lobby")!);
    }
    if(localStorage.getItem("player") !== null){
      this.player = JSON.parse(localStorage.getItem("player")!);
    }

    this.onLanguageChange("english");

    // Every 3 seconds fetch the current game state from the server
    setInterval(() => {
      if(!this.lobby) return;

      this.lobbyService.getLobbyState(this.lobby.id)
        .then(response => response.json())
        .then((data: LobbyStateResponse) => {
          this.lobby = {
            id: this.lobby!.id,
            players: data.players,
            gameState: data.gameState,
            lastWinner: data.lastWinner
          };
          console.log(this.lobby);
          localStorage.setItem("lobby", JSON.stringify(this.lobby));
          localStorage.setItem("player", JSON.stringify(this.player));
          this.gameStarted = this.lobby !== null && this.lobby.gameState !== null;
        });
    }, 1500);
  }

  onLanguageChange(language: string) {
    console.log("Language changed to: ", language);
    this.currentLanguage = language === "english" ? "en_US" : "pl_PL";
    this.lobbyService.getWords(this.currentLanguage)
      .then(response => response.json())
      .then((data: Object) => {
        this.words = data;
        console.log(data);
      });
  }

  joinLobby(e: Event) {
    e.preventDefault();
    e.stopPropagation();
    this.lobbyService.joinLobby(this.player.name)
      .then(response => response.json())
      .then((response: JoinLobbyResponse) => {
        this.lobby = response.lobby;
        this.player = response.player;
        localStorage.setItem("player", JSON.stringify(this.player));
        localStorage.setItem("lobby", JSON.stringify(this.lobby));

        console.log(JSON.stringify(this.lobby));
        console.log("im " + this.player);

        this.gameStarted = this.lobby !== null && this.lobby.gameState !== null;
      });
      // .then(text => text.text())
      // .then(text => console.log(text));
  }
  toggleReady($event: MouseEvent) {
    $event.preventDefault();
    $event.stopPropagation();
    if(!this.lobby)
      return;
    this.lobbyService.toggleReady(this.player, this.lobby)
      .then(response => response.json())
      .then((response) => {
        this.lobby = response.lobby;
        console.log(JSON.stringify(this.lobby));
        localStorage.setItem("lobby", JSON.stringify(this.lobby));
        this.player = this.lobby!.players.find(player => player.secret === this.player.secret)!;
        localStorage.setItem("player", JSON.stringify(this.player));

        this.gameStarted = this.lobby !== null && this.lobby.gameState !== null;
      });
      // .then(text => text.text())
      // .then(text => console.log(text));
  }
  leaveLobby($event: MouseEvent) {
    //TODO: probably should delete the player from the lobby in the db
    localStorage.clear();
    window.location.assign("/niwicki");
  }

  colorToString(color: Color): string{
    switch (color){
      case Color.Red:
        return "red";
      case Color.Blue:
        return "blue";
      case Color.Green:
        return "green";
      case Color.Yellow:
        return "yellow";
      default:
        return "";
    }
  }
}
