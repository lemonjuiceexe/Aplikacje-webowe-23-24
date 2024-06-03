import { drawBoard } from "./canvas.ts";

import './style.css';
import {Balloon, Field, ServerResponse} from "./types.ts";

const socket = new WebSocket('ws://127.0.0.1:46089');

let board: Array<Array<Field | Balloon>> = [];
let animation_tick = 0;

setInterval(() => {
    drawBoard(board, animation_tick++);
}, 500);

socket.onopen = () => {
    console.log('Connection with server established');
};

// Event listener for receiving messages from the server
socket.addEventListener('message', (event) => {
    const response: ServerResponse = JSON.parse(event.data);
    console.log('Received from server:', response);
    board = response.board;
    // drawBoard(response.board);
});

// Event listener for when the connection is closed
socket.addEventListener('close', (_) => {
    console.log('Connection closed');
});

// Event listener for handling errors
socket.addEventListener('error', (event) => {
    console.error('Connection error:', event);
});

document.querySelector("#btn")!.addEventListener("click", () => {
    socket.send("i have been clicked");
});