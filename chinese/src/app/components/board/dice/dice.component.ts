import {Component, EventEmitter, Input, Output} from '@angular/core';
import {GameService} from "../../../services/game.service";
import {Player} from "../../lobby/lobby.component";
import {NgForOf, NgIf, NgOptimizedImage} from "@angular/common";

@Component({
  selector: 'app-dice',
  standalone: true,
  imports: [
    NgIf,
    NgOptimizedImage,
    NgForOf
  ],
  templateUrl: './dice.component.html',
  styleUrl: './dice.component.css'
})
export class DiceComponent {
  @Input() player!: Player;
  @Input() lobbyId!: number | null;

  @Input() diceValue: number = 1;
  @Output() diceValueChange = new EventEmitter<number>();

  spritePaths: string[] = [
    'assets/dice/1.png',
    'assets/dice/2.png',
    'assets/dice/3.png',
    'assets/dice/4.png',
    'assets/dice/5.png',
    'assets/dice/6.png'
  ];

  constructor(private gameService: GameService) { }
  diceClick() {
    if (this.lobbyId === null) {
      console.error("Can't roll the dice: lobbyId is null");
      return;
    }

    this.gameService.rollDice(this.player, this.lobbyId)
      .then(response => response.json())
      .then((roll: number) => {
        console.log('dice roll response:', roll);
        this.diceValue = roll;
        this.diceValueChange.emit(roll);
      });
      // .then(response => response.text())
      // .then((text: string) => {
      //   console.log(text);
      // });
  }
}
