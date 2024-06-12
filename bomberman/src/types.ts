export enum Field {
    Empty = "-",
    Border = "*",
    Obstacle = "X",
    Player = "P",
}
export enum Direction {
    Up = 0,
    Right = 1,
    Down = 2,
    Left = 3,
}

export interface ServerResponse {
    id: string,
    board: Array<Array<Field | Balloon>>
}

export class Balloon {
    discriminator: 'balloon' = 'balloon';
    x: number;
    y: number;
    direction: Direction;
    move_percentage: number;
    last_horizontal_direction: Direction.Left | Direction.Right;

    constructor(x: number, y: number, direction: Direction, move_percentage: number, last_horizontal_direction: Direction.Left | Direction.Right) {
        this.x = x;
        this.y = y;
        this.direction = direction;
        this.move_percentage = move_percentage;
        this.last_horizontal_direction = last_horizontal_direction;
    }
}
export class Player {
    discriminator: 'player' = 'player';
    x: number;
    y: number;
    x_px: number;
    y_px: number;
    direction: Direction;
    animation_frame: number;

    constructor(x: number, y: number, x_px: number, y_px: number) {
        this.x = x;
        this.y = y;
        this.x_px = x_px;
        this.y_px = y_px;
        this.direction = Direction.Down;
        this.animation_frame = 0;
    }
}