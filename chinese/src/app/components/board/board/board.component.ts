import {Component, Input} from '@angular/core';
import {CellComponent} from "../cell/cell.component";
import {CommonModule} from "@angular/common";
import {PawnComponent} from "../pawn/pawn.component";
import {GameStateServer, Lobby, Player} from "../../lobby/lobby.component";
import {DiceComponent} from "../dice/dice.component";
import {GameService} from "../../../services/game.service";

export enum Color {
  Neutral,
  Red,
  Blue,
  Green,
  Yellow
}

interface Cell {
  x: number;
  y: number;
  color: Color;
  empty: boolean;
  childPawns: Pawn[];
  highlighted: boolean;
}

export interface Pawn {
  color: Color;
  path: number[];
  cellsTraveled: number;
  spawnCell: number;
  highlighted: boolean;
}

export interface GameStateClient {
  pawns: Pawn[];
  currentTurn: Color;
  diceValue: number;
  roundStartTimestamp: number;
}

export const backgroundColors: string[] = ["beige", "tomato", "cornflowerblue", "#32de84", "gold"];

@Component({
  selector: 'app-board',
  standalone: true,
  imports: [
    CellComponent,
    CommonModule,
    PawnComponent,
    DiceComponent
  ],
  templateUrl: './board.component.html',
  styleUrl: './board.component.css'
})
export class BoardComponent {
  @Input() player!: Player;
  @Input() set lobby(lobby: Lobby) {
    this.lobbyId = lobby.id;
    this.gameState = this.serverGameStateToClientGameState(lobby.gameState!);
    this.colorsInGame = lobby.players.map(player => player.color);

    this.refreshBoard();
  }
  readonly roundDuration: number = 60;

  lobbyId: number | null = null;
  gameState: GameStateClient | null = null;
  colorsInGame: Color[] = [Color.Red, Color.Blue, Color.Green, Color.Yellow];

  cells: Cell[] = [];

  highlightedCell: number | null = null;
  highlightedPawns: Pawn[] = [];
  roundTimeLeft: number = 10;

  redHouses: number[] = [11 * 5 + 1, 11 * 5 + 2, 11 * 5 + 3, 11 * 5 + 4];
  blueHouses: number[] = [11 * 5 + 6, 11 * 5 + 7, 11 * 5 + 8, 11 * 5 + 9].reverse();
  yellowHouses: number[] = [11 * 1 + 5, 11 * 2 + 5, 11 * 3 + 5, 11 * 4 + 5];
  greenHouses: number[] = [11 * 6 + 5, 11 * 7 + 5, 11 * 8 + 5, 11 * 9 + 5].reverse();

  redSpawns = [0, 1, 11 + 0, 11 + 1];
  blueSpawns = [11 * 9 + 9, 11 * 9 + 10, 11 * 10 + 9, 11 * 10 + 10];
  greenSpawns = [11 * 9, 11 * 9 + 1, 11 * 10, 11 * 10 + 1];
  yellowSpawns = [9, 10, 11 + 9, 11 + 10];

  basePath: number[] = [
    4 * 11 + 0, 4 * 11 + 1, 4 * 11 + 2, 4 * 11 + 3, 4 * 11 + 4,
    3 * 11 + 4, 2 * 11 + 4, 1 * 11 + 4,
    0 * 11 + 4, 0 * 11 + 5, 0 * 11 + 6,
    1 * 11 + 6, 2 * 11 + 6, 3 * 11 + 6, 4 * 11 + 6,
    4 * 11 + 7, 4 * 11 + 8, 4 * 11 + 9,
    4 * 11 + 10, 5 * 11 + 10, 6 * 11 + 10,
    6 * 11 + 9, 6 * 11 + 8, 6 * 11 + 7,
    6 * 11 + 6, 7 * 11 + 6, 8 * 11 + 6, 9 * 11 + 6,
    10 * 11 + 6, 10 * 11 + 5, 10 * 11 + 4,
    9 * 11 + 4, 8 * 11 + 4, 7 * 11 + 4, 6 * 11 + 4,
    6 * 11 + 3, 6 * 11 + 2, 6 * 11 + 1,
    6 * 11 + 0, 5 * 11 + 0
  ];
  redPath: number[] = this.basePath.concat(this.redHouses);
  yellowPath: number[] = this.basePath.slice(10).concat(this.basePath.slice(0, 10)).concat(this.yellowHouses);
  bluePath: number[] = this.basePath.slice(20).concat(this.basePath.slice(0, 20)).concat(this.blueHouses);
  greenPath: number[] = this.basePath.slice(30).concat(this.basePath.slice(0, 30)).concat(this.greenHouses);

  constructor(private gameService: GameService) {}
  ngOnInit() {
    //region ---- Generate board logic ----
    // Special cells
    const redSpecial = [11 * 4 + 0];
    const blueSpecial = [11 * 6 + 10];
    const yellowSpecial = [11 * 0 + 6];
    const greenSpecial = [11 * 10 + 4];
    // Empty cells
    const emptyCells = [
      11 * 0 + 2, 11 * 0 + 3, 11 * 0 + 7, 11 * 0 + 8,
      11 * 1 + 2, 11 * 1 + 3, 11 * 1 + 7, 11 * 1 + 8,
      11 * 2 + 0, 11 * 2 + 1, 11 * 2 + 2, 11 * 2 + 3, 11 * 2 + 7, 11 * 2 + 8, 11 * 2 + 9, 11 * 2 + 10,
      11 * 3 + 0, 11 * 3 + 1, 11 * 3 + 2, 11 * 3 + 3, 11 * 3 + 7, 11 * 3 + 8, 11 * 3 + 9, 11 * 3 + 10,
      11 * 7 + 0, 11 * 7 + 1, 11 * 7 + 2, 11 * 7 + 3, 11 * 7 + 7, 11 * 7 + 8, 11 * 7 + 9, 11 * 7 + 10,
      11 * 8 + 0, 11 * 8 + 1, 11 * 8 + 2, 11 * 8 + 3, 11 * 8 + 7, 11 * 8 + 8, 11 * 8 + 9, 11 * 8 + 10,
      11 * 9 + 2, 11 * 9 + 3, 11 * 9 + 7, 11 * 9 + 8,
      11 * 10 + 2, 11 * 10 + 3, 11 * 10 + 7, 11 * 10 + 8
    ];

    // Set interval to refresh the round clock
    setInterval(() => {
      if (!this.gameState) return;
      // PHP timestamp is in seconds, JS timestamp is in milliseconds
      const timePassed = Math.floor(((new Date().getTime() / 1000) - this.gameState.roundStartTimestamp));
      this.roundTimeLeft = this.roundDuration - timePassed;
    }, 900);

    // Generate the board
    for (let i = 0; i < 11; i++)
      for (let j = 0; j < 11; j++) {
        let color: Color = Color.Neutral;
        let empty: boolean = false;

        //region Decide the color of the cell
        if (
          this.redSpawns.concat(this.redHouses).concat(redSpecial)
            .includes(i * 11 + j))
          color = Color.Red;
        else if (
          this.blueSpawns.concat(this.blueHouses).concat(blueSpecial)
            .includes(i * 11 + j))
          color = Color.Blue;
        else if (
          this.greenSpawns.concat(this.greenHouses).concat(greenSpecial)
            .includes(i * 11 + j))
          color = Color.Green;
        else if (
          this.yellowSpawns.concat(this.yellowHouses).concat(yellowSpecial)
            .includes(i * 11 + j))
          color = Color.Yellow;
        //endregion
        if (emptyCells.includes(i * 11 + j))
          empty = true;

        this.cells.push({
          x: j,
          y: i,
          color: color,
          empty: empty,
          childPawns: [],
          highlighted: false
        });

        this.refreshBoard();
      }
    //endregion
  }

  diceValueChange(value: number): void {
    this.gameState!.diceValue = value;
  }
  highlightLegalPawnsOnChange(pawnsTraveledValues: number[]): void {
    console.log(pawnsTraveledValues);
    this.gameState!.pawns = this.gameState!.pawns
      .filter((pawn: Pawn) => pawn.color === this.player.color)
      .map((pawn: Pawn) => {
        return {
          ...pawn,
          highlighted: pawnsTraveledValues.includes(pawn.cellsTraveled)
        }
      });
    this.highlightedPawns = this.gameState!.pawns.filter(pawn => pawn.highlighted);
  }

  updatePawnOnClick(clickedPawn: Pawn): void {
    if(clickedPawn.color !== this.player!.color) return;

    const pawnIndex = this.gameState!.pawns.findIndex(pawn => pawn.color === clickedPawn.color);

    this.gameService.movePawn(this.player, this.lobbyId!, clickedPawn)
      .then(response => response.json())
      .then((data: GameStateServer) => {
        this.gameState = this.serverGameStateToClientGameState(data);
        this.gameState!.pawns[pawnIndex] = clickedPawn;
        this.refreshBoard();
      });
      // .then(response => response.text())
      // .then(response => console.log(response));
    this.highlightedPawns = [];
  }
  updatePawnOnHover(hoveredPawn: Pawn | null): void {
    if(hoveredPawn === null) {
      this.highlightedCell = null;
      this.refreshBoard();
      return;
    }
    if(hoveredPawn.color !== this.player!.color) return;
    if(!this.highlightedPawns.some((pawn: Pawn) =>
      pawn.color === hoveredPawn.color && pawn.cellsTraveled === hoveredPawn.cellsTraveled)) return;

    const nextCellIndex = hoveredPawn.cellsTraveled === 0 ? 1 : hoveredPawn.cellsTraveled + this.gameState!.diceValue;
    this.highlightedCell = hoveredPawn.path[nextCellIndex - 1];
    this.refreshBoard();
  }

  refreshBoard(): void {
    // ---- Update pawns positions ----
    this.cells.forEach((cell: Cell) => {
      cell.childPawns = this.gameState!.pawns.filter(pawn => {
        if(!this.colorsInGame.includes(pawn.color)) return false;

        let pawnsPath: number[] = [];
        switch (pawn.color) {
          case Color.Red:
            pawnsPath = this.redPath;
            break;
          case Color.Blue:
            pawnsPath = this.bluePath;
            break;
          case Color.Green:
            pawnsPath = this.greenPath;
            break;
          case Color.Yellow:
            pawnsPath = this.yellowPath;
            break;
        }
        const pawnsCoords: { x: number, y: number } = this.cellsTraveledToCoords(pawn.cellsTraveled, pawnsPath, pawn.spawnCell);
        return pawnsCoords.x === cell.x && pawnsCoords.y === cell.y;
      });
    });
    // ---- Update highlighted cells ----
    this.cells.forEach((cell: Cell) => {
      cell.highlighted = this.highlightedCell === (cell.x + cell.y * 11);
    });
  }

  cellsTraveledToCoords(cellsTraveled: number, path: number[], spawnCell: number): { x: number, y: number } {
    let currentCell: number = 0;
    currentCell = cellsTraveled === 0 ? spawnCell : path[cellsTraveled - 1];

    const x = currentCell % 11;
    const y = Math.floor(currentCell / 11);
    return {
      x: x,
      y: y
    }
  }

  serverGameStateToClientGameState(serverState: GameStateServer): GameStateClient {
    let clientGameState: GameStateClient = {
      pawns: [],
      currentTurn: serverState.currentTurn,
      diceValue: serverState.diceValue,
      roundStartTimestamp: serverState.roundStartTimestamp
    };
    [serverState.redTravelled, serverState.blueTravelled, serverState.greenTravelled, serverState.yellowTravelled]
      .forEach((travelled: number[], colorIndex: number) => {
        travelled.forEach((travelled: number, pawnIndex: number) => {
          clientGameState.pawns.push({
              color: (colorIndex + 1) as Color,
              path: [this.redPath, this.bluePath, this.greenPath, this.yellowPath][colorIndex],
              cellsTraveled: travelled,
              spawnCell: [this.redSpawns, this.blueSpawns, this.greenSpawns, this.yellowSpawns][colorIndex][pawnIndex],
              highlighted: this.highlightedPawns.some(pawn => pawn.color === (colorIndex + 1) && pawn.cellsTraveled === travelled)
            }
          );
        })
      });

    return clientGameState;
  }

}
