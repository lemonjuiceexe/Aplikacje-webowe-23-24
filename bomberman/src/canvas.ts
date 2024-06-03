import {Balloon, Direction, Field} from "./types.ts";

export function drawBoard(board: Array<Array<Field | Balloon>>, animation_tick: number): void {
    const canvas = document.getElementById('canvas') as HTMLCanvasElement;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    for (const [y, row] of board.entries()) {
        for (const [x, field] of row.entries()) {
            // draw different image based on field
            const image = new Image();

            if(typeof field === 'object') {
                const balloon = field as Balloon;
                const balloon_animation_frame = Math.abs(2 - (animation_tick % 4));
                switch (balloon.last_horizontal_direction) {
                    case Direction.Left:
                        image.src = `animations/balloon/left_${balloon_animation_frame}.png`;
                        break;
                    case Direction.Right:
                        image.src = `animations/balloon/right_${balloon_animation_frame}.png`;
                        break;
                    default:
                        console.log("my fucking direction's ", balloon.last_horizontal_direction);
                        image.src = 'animations/balloon/balloon.png';
                        break;
                }
                console.log(image.src);
            }
            else{
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
            }


            image.onload = () => {
                ctx.drawImage(image, x * 32, y * 32, 32, 32);
            };
        }
    }
}