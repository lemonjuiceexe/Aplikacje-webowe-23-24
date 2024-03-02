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
  @Input() highlighted: boolean = false;

  @Output() pawnClicked = new EventEmitter<Pawn>();
  @Output() pawnHovered = new EventEmitter<Pawn | null>();

  backgroundColor: string = backgroundColors[0];
  display: string = "inline-grid";
  shadow: string = this.highlighted ? "0 0 10px 5px #ff0000" : "0 0 0 0 #ff0000";

  ngOnInit() {
    this.backgroundColor = backgroundColors[(this.color)];
    this.display = this.empty ? "none" : "inline-grid";
  }
  ngOnChanges(){
    this.shadow = this.highlighted ? "0 0 10px 5px #ff0000" : "0 0 0 0 #ff0000";
  }

  onPawnClick(pawn: Pawn){
    this.pawnClicked.emit(pawn);
  }
  onPawnHover(pawn: Pawn | null){
    this.pawnHovered.emit(pawn);
  }
}
