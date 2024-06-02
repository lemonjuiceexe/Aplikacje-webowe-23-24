import {Field} from "./types.ts";

export function drawBoard(board: Array<Array<Field>>): void {
    const canvas = document.getElementById('canvas') as HTMLCanvasElement;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    for (const [y, row] of board.entries()) {
        for (const [x, field] of row.entries()) {
            // draw different image based on field
            const image = new Image();
            switch (field) {
                case Field.Empty:
                    image.src = 'animations/background.png';
                    break;
                case Field.Border:
                    image.src = 'animations/border.png';
                    break;
                case Field.Obstacle:
                    image.src = 'animations/obstacle/obstacle.png';
                    break;
                default:
                    image.src = 'animations/background.png';
                    break;
            }
            image.onload = () => {
                ctx.drawImage(image, x * 32, y * 32, 32, 32);
            };
        }
    }
}