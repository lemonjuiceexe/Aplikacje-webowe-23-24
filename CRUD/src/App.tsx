import { useState } from "react"
import reactLogo from "./assets/react.svg"
import viteLogo from "/vite.svg"
import "./App.css"
import List from "./components/List.jsx";
import Card from "./components/ui/Card.jsx";

function App() {
    return (
        <>
            <h1 className="
                text-3xl
                font-bold
                leading-9
                text-gray-900
                sm:text-4xl
                sm:leading-10
                mb-6
                p-4
            ">
                Movies
            </h1>
            <Card>
                <List/>
            </Card>
        </>
    );
}

export default App
