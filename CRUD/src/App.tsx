import "./App.css"
import MovieList from "./components/MovieList.tsx";
import Card from "./components/ui/Card.jsx";

function App() {
    return (
        <>
            <div className="btn btn-primary">
                asdas
            </div>
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
               Motion pictures
            </h1>
            <Card>
                <MovieList/>
            </Card>
        </>
    );
}

export default App
