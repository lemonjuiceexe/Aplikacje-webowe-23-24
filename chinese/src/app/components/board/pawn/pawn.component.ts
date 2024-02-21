import {Component, EventEmitter, Input, Output} from '@angular/core';
import {Color, backgroundColors, BoardComponent, IPawn} from "../board/board.component";

@Component({
  selector: 'app-pawn',
  standalone: true,
  imports: [],
  templateUrl: './pawn.component.html',
  styleUrl: './pawn.component.css'
})
export class PawnComponent {
  // @Input() color: Color = Color.Neutral;
  @Input() pawn: IPawn = {color: Color.Neutral, path: [], cellsTraveled: 0};
  backgroundColor: string = backgroundColors[0];

  @Output() pawnClicked = new EventEmitter<IPawn>();

  ngOnInit(){
    this.backgroundColor = backgroundColors[this.pawn.color];
  }

  onClick(){
    this.pawn.cellsTraveled++;
    this.pawnClicked.emit(this.pawn);
  }
}
