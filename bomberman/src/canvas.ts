import { Balloon, Direction, Field } from "./types.ts";

const fieldImages = {
    [Field.Empty]: new Image(),
    [Field.Border]: new Image(),
    [Field.Obstacle]: new Image(),
    default: new Image()
};

const balloonImages = {
    left: [new Image(), new Image(), new Image(), new Image()],
    right: [new Image(), new Image(), new Image(), new Image()],
    default: new Image()
};

//#region PRELOAD IMAGES
fieldImages[Field.Empty].src = 'animations/background.png';
fieldImages[Field.Border].src = 'animations/border.png';
fieldImages[Field.Obstacle].src = 'animations/obstacle/obstacle.png';
fieldImages.default.src = 'animations/background.png';

for (let i = 0; i < 4; i++) {
    balloonImages.left[i].src = `animations/balloon/left_${i}.png`;
    balloonImages.right[i].src = `animations/balloon/right_${i}.png`;
}
balloonImages.default.src = 'animations/balloon/balloon.png';
//#endregion


export function drawBoard(board: Array<Array<Field | Balloon>>, animation_tick: number): void {
    const canvas = document.getElementById('canvas') as HTMLCanvasElement;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    // Draw the background and field images first
    drawFields(ctx, board);

    // Draw the balloons after fields are drawn to avoid flickering
    drawBalloons(ctx, board, animation_tick);
}

function drawFields(ctx: CanvasRenderingContext2D, board: Array<Array<Field | Balloon>>) {
    for (const [y, row] of board.entries()) {
        for (const [x, field] of row.entries()) {
            const image = fieldImages[field] || fieldImages.default;
            ctx.drawImage(image, x * 32, y * 32, 32, 32);
        }
    }
}

function drawBalloons(ctx: CanvasRenderingContext2D, board: Array<Array<Field | Balloon>>, animation_tick: number) {
    for (const [y, row] of board.entries()) {
        for (const [x, field] of row.entries()) {
            let offsetY = 0, offsetX = 0;
            if (typeof field === 'object') {
                const balloon = field as Balloon;
                const balloon_animation_frame = Math.abs(2 - (animation_tick % 4));
                let image = balloonImages.default;
                switch (balloon.last_horizontal_direction) {
                    case Direction.Left:
                        image = balloonImages.left[balloon_animation_frame];
                        break;
                    case Direction.Right:
                        image = balloonImages.right[balloon_animation_frame];
                        break;
                }
                // Add offset based on balloon's move percentage
                switch (balloon.direction) {
                    case Direction.Left:
                        offsetX = -(balloon.move_percentage / 100) * 32;
                        break;
                    case Direction.Right:
                        offsetX = (balloon.move_percentage / 100) * 32;
                        break;
                    case Direction.Up:
                        offsetY = -(balloon.move_percentage / 100) * 32;
                        break;
                    case Direction.Down:
                        offsetY = (balloon.move_percentage / 100) * 32;
                        break;
                }
                ctx.drawImage(image, x * 32 + offsetX, y * 32 + offsetY, 32, 32);
            }
        }
    }
}

export function balloonsSmoothMove(board: Array<Array<Field | Balloon>>){
    for (const [y, row] of board.entries()) {
        for (const [x, field] of row.entries()) {
            if (typeof field === 'object') {
                const balloon = field as Balloon;
                balloon.move_percentage += 1.5;
                board[y][x] = balloon;
            }
        }
    }
}