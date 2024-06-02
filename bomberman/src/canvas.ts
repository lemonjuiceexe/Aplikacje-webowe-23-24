import {Field} from "./types.ts";

export function drawBoard(board: Array<Array<Field>>): void {
  const canvas = document.getElementById('canvas') as HTMLCanvasElement;
  const ctx = canvas.getContext('2d');
  if (!ctx) return;

  for(const [y, row] of board.entries()) {
    for(const [x, field] of row.entries()) {
      ctx.fillStyle = field === Field.Empty ? 'white' : 'black';
      ctx.fillRect(x * 20, y * 20, 20, 20);
    }
  }
}