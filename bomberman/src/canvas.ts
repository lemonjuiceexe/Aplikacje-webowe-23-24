import {Balloon, Direction, Field, Player} from "./types.ts";

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

const garlicImages = {
    left: [new Image(), new Image(), new Image()],
    right: [new Image(), new Image(), new Image()],
    default: new Image()
};

const playerImages = {
    [Direction.Up]: [new Image(), new Image(), new Image(), new Image()],
    [Direction.Right]: [new Image(), new Image(), new Image(), new Image()],
    [Direction.Down]: [new Image(), new Image(), new Image(), new Image()],
    [Direction.Left]: [new Image(), new Image(), new Image(), new Image()],
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

for (let i = 0; i < 3; i++) {
    garlicImages.left[i].src = `animations/garlic/left_${i}.png`;
    garlicImages.right[i].src = `animations/garlic/right_${i}.png`;
}
garlicImages.default.src = 'animations/garlic/garlic.png';

for (let i = 0; i < 3; i++) {
    playerImages[Direction.Up][i].src = `animations/player/up_${i}.png`;
    playerImages[Direction.Right][i].src = `animations/player/right_${i}.png`;
    playerImages[Direction.Down][i].src = `animations/player/down_${i}.png`;
    playerImages[Direction.Left][i].src = `animations/player/left_${i}.png`;
}
playerImages.default.src = 'animations/player/down_0.png';
//#endregion


export function drawBoard(board: Array<Array<Field | Balloon | Player>>, animation_tick: number): void {
    const canvas = document.getElementById('canvas') as HTMLCanvasElement;
    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    drawFields(ctx, board);
    drawBalloons(ctx, board, animation_tick);
    drawGarlics(ctx, board, animation_tick);
    drawPlayers(ctx, board);
}

function drawFields(ctx: CanvasRenderingContext2D, board: Array<Array<Field | Balloon | Player>>) {
    for (const [y, row] of board.entries()) {
        for (const [x, field] of row.entries()) {
            const image = fieldImages[field] || fieldImages.default;
            ctx.drawImage(image, x * 32, y * 32, 32, 32);
        }
    }
}

function drawBalloons(ctx: CanvasRenderingContext2D, board: Array<Array<Field | Balloon | Player>>, animation_tick: number) {
    for (const [y, row] of board.entries()) {
        for (const [x, field] of row.entries()) {
            let offsetY = 0, offsetX = 0;
            if (typeof field === 'object' && field.discriminator === 'balloon') {
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
function drawGarlics(ctx: CanvasRenderingContext2D, board: Array<Array<Field | Balloon | Player>>, animation_tick: number) {
    for (const [y, row] of board.entries()) {
        for (const [x, field] of row.entries()) {
            let offsetY = 0, offsetX = 0;
            if (typeof field === 'object' && field.discriminator === 'garlic') {
                const garlic = field as Balloon;
                const garlic_animation_frame = Math.abs(1 - (animation_tick % 4));
                let image = garlicImages.default;
                switch (garlic.last_horizontal_direction) {
                    case Direction.Left:
                        image = garlicImages.left[garlic_animation_frame];
                        break;
                    case Direction.Right:
                        image = garlicImages.right[garlic_animation_frame];
                        break;
                }
                // Add offset based on garlic's move percentage
                switch (garlic.direction) {
                    case Direction.Left:
                        offsetX = -(garlic.move_percentage / 100) * 32;
                        break;
                    case Direction.Right:
                        offsetX = (garlic.move_percentage / 100) * 32;
                        break;
                    case Direction.Up:
                        offsetY = -(garlic.move_percentage / 100) * 32;
                        break;
                    case Direction.Down:
                        offsetY = (garlic.move_percentage / 100) * 32;
                        break;
                }
                ctx.drawImage(image, x * 32 + offsetX, y * 32 + offsetY, 32, 32);
            }
        }
    }

}

function drawPlayers(ctx: CanvasRenderingContext2D, board: Array<Array<Field | Balloon | Player>>){
    for(const [_, row] of board.entries()){
        for(const [_, field] of row.entries()){
            if(typeof field === 'object' && field.discriminator === 'player'){
                const player = field as Player;
                const image = playerImages[player.direction][player.animation_frame];
                ctx.drawImage(image, player.x_px, player.y_px, 32, 32);
            }
        }
    }
}

export function balloonsSmoothMoveStep(board: Array<Array<Field | Balloon | Player>>){
    for (const [y, row] of board.entries()) {
        for (const [x, field] of row.entries()) {
            if (typeof field === 'object' && field.discriminator === 'balloon') {
                const balloon = field as Balloon;
                balloon.move_percentage += .5;
                board[y][x] = balloon;
            }
        }
    }
}