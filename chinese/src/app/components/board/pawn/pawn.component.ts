import {Component, EventEmitter, Input, Output} from '@angular/core';
import {Color, backgroundColors, BoardComponent, Pawn} from "../board/board.component";

@Component({
  selector: 'app-pawn',
  standalone: true,
  imports: [],
  templateUrl: './pawn.component.html',
  styleUrl: './pawn.component.css'
})
export class PawnComponent {
  @Input() pawn: Pawn = {color: Color.Neutral, path: [], cellsTraveled: 0, spawnCell: 0};
  backgroundColor: string = backgroundColors[0];

  @Output() pawnClicked = new EventEmitter<Pawn>();
  @Output() pawnHovered = new EventEmitter<Pawn | null>();

  ngOnInit(){
    this.backgroundColor = backgroundColors[this.pawn.color];
  }

  onClick(){
    this.pawnClicked.emit(this.pawn);
  }
  onMouseEnter() {
    this.pawnHovered.emit(this.pawn);
  }
  onMouseLeave() {
    this.pawnHovered.emit(null);
  }
}
