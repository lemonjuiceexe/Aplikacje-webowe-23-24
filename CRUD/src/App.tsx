import "./App.css"
import MovieList from "./components/MovieList.tsx";
import Card from "./components/ui/Card.jsx";
import {useRef, useState} from "react";
import NewMovieDialog from "./components/NewMovieDialog.tsx";

export interface NewMovie {
    title: string;
    year: number;
    director: string;
    rating: number;
    length: string;
    count: number;
}
export interface Movie {
    id: number;
    title: string;
    year: number;
    length: string;
    director_id: number;
    count: number;
    rating: number;
}
export interface Director {
    id: number;
    name: string;
}

const DUMMY_MOVIES: Movie[] = [
    {
        id: 1,
        title: "The Shawshank Redemption",
        year: 1994,
        length: "2:22:21",
        director_id: 1,
        count: 8,
        rating: 4
    },
    {
        id: 2,
        title: "The Godfather",
        year: 1972,
        length: "2:55:00",
        director_id: 2,
        count: 2,
        rating: 5
    },
    {
        id: 3,
        title: "The Dark Knight",
        year: 2008,
        length: "2:32:00",
        director_id: 3,
        count: 22,
        rating: 3
    }
];
const directors: Director[] = [
    {
        id: 1,
        name: "Frank Darabont"
    },
    {
        id: 2,
        name: "Francis Ford Coppola"
    },
    {
        id: 3,
        name: "Christopher Nolan"
    }
];

function App() {
    const newMovieRef = useRef<HTMLDialogElement>(null);
    const [movies, setMovies] = useState<Movie[]>(DUMMY_MOVIES);

    function addNewMovie(movie: NewMovie){
        console.log("Adding new movie: ", movie);

        const newMovie: Movie = {
            id: Math.max(...movies.map(movie => movie.id)) + 1,
            title: movie.title,
            year: movie.year,
            length: movie.length,
            director_id: directors.find(director => director.name === movie.director)?.id ?? 1,
            count: movie.count,
            rating: movie.rating
        };
        setMovies(prevMovies => prevMovies.concat(newMovie));
    }
    function deleteMovie(movieId: number){
        console.log("Deleting movie with id: ", movieId);
        setMovies(prevMovies => prevMovies.filter(movie => movie.id !== movieId));
    }

    return (
        <>
            <NewMovieDialog ref={newMovieRef} addNewMovie={addNewMovie}/>
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
            <div className="indicator">
                <span className="indicator-item">
                    <button className="btn btn-circle bg-accent text-3xl pb-1"
                            onClick={() => newMovieRef.current!.showModal()}
                    >+</button>
                </span>
                <Card>
                    <MovieList movies={movies} directors={directors} deleteMovie={deleteMovie}/>
                </Card>
            </div>
        </>

    );
}

export default App
