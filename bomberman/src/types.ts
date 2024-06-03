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

export interface Baloon{
    x: number,
    y: number,
    direction: Direction,
    move_percentage: number,
}

export interface ServerResponse {
    id: string,
    board: Array<Array<Field | Baloon>>
}