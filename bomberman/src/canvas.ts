import {Balloon, Direction, Field} from "./types.ts";

export function drawBoard(board: Array<Array<Field | Balloon>>, animation_tick: number): void {
    const canvas = document.getElementById('canvas') as HTMLCanvasElement;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    for (const [y, row] of board.entries()) {
        for (const [x, field] of row.entries()) {
            // draw different image based on field
            const image = new Image();
            const background = new Image();
            background.src = 'animations/background.png';

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
    drawBalloons(ctx);

    function drawBalloons(ctx: CanvasRenderingContext2D) {
        for (const [y, row] of board.entries()) {
            for (const [x, field] of row.entries()) {
                let offsetY = 0, offsetX = 0;
                if (typeof field === 'object') {
                    const balloon = field as Balloon;
                    const balloon_animation_frame = Math.abs(2 - (animation_tick % 4));
                    const image = new Image();
                    switch (balloon.last_horizontal_direction) {
                        case Direction.Left:
                            image.src = `animations/balloon/left_${balloon_animation_frame}.png`;
                            break;
                        case Direction.Right:
                            image.src = `animations/balloon/right_${balloon_animation_frame}.png`;
                            break;
                        default:
                            image.src = 'animations/balloon/balloon.png';
                            break;
                    }
                    // add offset based on balloon's move percentage
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

                    image.onload = () => {
                        ctx.drawImage(image, x * 32 + offsetX, y * 32 + offsetY, 32, 32);
                    };
                }
            }
        }
    }
}