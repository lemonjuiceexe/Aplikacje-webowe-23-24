import {Component, EventEmitter, Input, Output} from '@angular/core';
import {Color, backgroundColors, Pawn} from '../board/board.component';
import {NgClass, NgForOf, NgStyle} from "@angular/common";
import {PawnComponent} from "../pawn/pawn.component";

@Component({
  selector: 'app-cell',
  standalone: true,
  imports: [
    NgClass,
    NgStyle,
    PawnComponent,
    NgForOf
  ],
  templateUrl: './cell.component.html',
  styleUrl: './cell.component.css'
})
export class CellComponent {
  @Input() x: number = 0;
  @Input() y: number = 0;
  @Input() color: Color = Color.Neutral;
  @Input() empty: boolean = false;
  @Input() childPawns: Pawn[] = [];

  @Output() pawnClicked = new EventEmitter<Pawn>();

  backgroundColor: string = backgroundColors[0];
  display: string = "inline-grid";

  ngOnInit() {
    this.backgroundColor = backgroundColors[(this.color)];
    this.display = this.empty ? "none" : "inline-grid";
  }

  onPawnClick(pawn: Pawn){
    this.pawnClicked.emit(pawn);
  }
}
