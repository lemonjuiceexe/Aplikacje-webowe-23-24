import {balloonsSmoothMoveStep, drawBoard} from "./canvas.ts";

import './style.css';
import {Balloon, Field, Garlic, Player, ServerResponse} from "./types.ts";

const socket = new WebSocket('ws://127.0.0.1:46089');

let board: Array<Array<Field | Balloon | Player>> = [];
let animation_tick = 0;

setInterval(() => {
    balloonsSmoothMoveStep(board);
    drawBoard(board, animation_tick++);
}, 100);

socket.onopen = () => {
    console.log('Connection with server established');
};

// Event listener for receiving messages from the server
socket.addEventListener('message', (event) => {
    const response: ServerResponse = JSON.parse(event.data);
    // console.log('Received from server:', response);
    board = response.board;
    // board.map(row => row.map(field => {
    //     if (field instanceof Balloon) {
    //         field.move_percentage = 0;
    //     }
    //
    //     return field;
    // }));
    console.log(board.flat().filter(field => typeof field === 'object' && field.discriminator === 'garlic')[0].direction);
    balloonsSmoothMoveStep(board);
    drawBoard(board, animation_tick);
});

// Event listener for when the connection is closed
socket.addEventListener('close', (_) => {
    console.log('Connection closed');
});

// Event listener for handling errors
socket.addEventListener('error', (event) => {
    console.error('Connection error:', event);
});

document.addEventListener('keydown', (event) => {
    const key = event.key;
    if (key === 'ArrowUp' || key === 'ArrowRight' || key === 'ArrowDown' || key === 'ArrowLeft') {
        const message = {
            key: key
        };
        socket.send(JSON.stringify(message));
    }
});