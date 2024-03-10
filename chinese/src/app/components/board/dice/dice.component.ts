import {Component, EventEmitter, Input, Output} from '@angular/core';
import {GameService} from "../../../services/game.service";
import {Player} from "../../lobby/lobby.component";
import {NgForOf, NgIf, NgOptimizedImage} from "@angular/common";
import {FormsModule} from "@angular/forms";

interface DiceRollResponse {
  roll: number;
  legalPawns: number[];
}

@Component({
  selector: 'app-dice',
  standalone: true,
  imports: [
    NgIf,
    NgOptimizedImage,
    NgForOf,
    FormsModule
  ],
  templateUrl: './dice.component.html',
  styleUrl: './dice.component.css'
})
export class DiceComponent {
  @Input() player!: Player;
  @Input() lobbyId!: number | null;

  @Input() words: any;
  @Input() diceValue: number = 1;
  @Output() diceValueChange = new EventEmitter<number>();
  // The cellsTraveled values of the pawns that can be moved
  @Output() legalPawns = new EventEmitter<number[]>();
  @Output() languageChange = new EventEmitter<string>();

  spritePaths: string[] = [
    'assets/dice/1.png',
    'assets/dice/2.png',
    'assets/dice/3.png',
    'assets/dice/4.png',
    'assets/dice/5.png',
    'assets/dice/6.png'
  ];
  language: string = 'english';

  constructor(private gameService: GameService) { }
  diceClick() {
    this.languageChange.emit(this.language);
    if (this.lobbyId === null) {
      console.error("Can't roll the dice: lobbyId is null");
      return;
    }

    this.gameService.rollDice(this.player, this.lobbyId)
      .then(response => response.json())
      .then((data: DiceRollResponse) => {
        this.diceValue = data.roll;
        this.diceValueChange.emit(data.roll);
        this.legalPawns.emit(data.legalPawns);

        let tts = new SpeechSynthesisUtterance("   " + data.roll.toString());
        tts.voice = speechSynthesis.getVoices()[49];
        tts.lang = {english: 'en-UK', polski: 'pl-PL'}[this.language]!;
        console.log(tts.lang);
        window.speechSynthesis.speak(tts);
      });
      // .then(response => response.text())
      // .then((text: string) => {
      //   console.log(text);
      // });
  }
}
