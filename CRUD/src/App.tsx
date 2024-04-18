import "./App.css"
import MovieList from "./components/MovieList.tsx";
import Card from "./components/ui/Card.jsx";
import {useEffect, useRef, useState} from "react";
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
    movie_count: number;
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
const DUMMY_DIRECTORS: Director[] = [
    {
        id: 1,
        name: "Frank Darabont",
        movie_count: 1
    },
    {
        id: 2,
        name: "Francis Ford Coppola",
        movie_count: 2
    },
    {
        id: 3,
        name: "Christopher Nolan",
        movie_count: 4
    }
];

function App() {
    const newMovieRef = useRef<HTMLDialogElement>(null);
    const [movies, setMovies] = useState<Movie[]>(DUMMY_MOVIES);
    const [directors, setDirectors] = useState<Director[]>(DUMMY_DIRECTORS);

    function refreshMovies() {
        fetch("http://localhost/movies/get_movies.php")
            .then(response => response.json())
            .then(data => {
                console.log("got ", data);
                setMovies([]);
                setMovies(data);
            });
            // .then(response => response.text())
            // .then(data => console.log(data));0
    }
    function refreshDirectors() {
        return fetch("http://localhost/movies/get_directors.php");
            // .then(response => response.text())
            // .then(data => console.log(data));
    }

    useEffect(() => {
        refreshMovies();
        refreshDirectors()
            .then(response => response.json())
            .then(data => {
                setDirectors(data);
            });
    }, []);

    async function addNewMovie(movie: NewMovie){
        if(!directors.some(director => director.name === movie.director)){
            await addNewDirector({ id: 0, name: movie.director, movie_count: 1});
        }

        const res = await refreshDirectors();
        const refreshed: Director[] = await res.json();
        setDirectors(refreshed);
        const newMovie: Movie = {
            id: Math.max(...movies.map(movie => movie.id)) + 1,
            title: movie.title,
            year: movie.year,
            length: movie.length,
            director_id: refreshed.find(director => director.name === movie.director)?.id ?? 1,
            count: movie.count,
            rating: movie.rating
        };

        const formData = new FormData();
        formData.append("title", newMovie.title);
        formData.append("year", newMovie.year.toString());
        formData.append("length", newMovie.length);
        formData.append("director_id", newMovie.director_id.toString());
        formData.append("count", newMovie.count.toString());
        formData.append("rating", newMovie.rating.toString());
        fetch("http://localhost/movies/add_movie.php", {
            method: "POST",
            body: formData
        })
            .then(() => refreshMovies());
        // setMovies(prevMovies => prevMovies.concat(newMovie));
    }
    async function addNewDirector(director: Director){
        const formData = new FormData();
        formData.append("name", director.name);
        formData.append("movie_count", director.movie_count.toString());

        await fetch("http://localhost/movies/add_director.php", {
            method: "POST",
            body: formData
        });
        const directors = await fetch("http://localhost/movies/get_directors.php")
        const newDirectors: Director[] = await directors.json();
        setDirectors(newDirectors);
    }

    function editMovie(editedMovie: Movie) {
        console.log("Editing movie: ", editedMovie);
        const formData = new FormData();
        formData.append("id", editedMovie.id.toString());
        formData.append("title", editedMovie.title);
        formData.append("year", editedMovie.year.toString());
        formData.append("length", editedMovie.length);
        formData.append("director_id", editedMovie.director_id.toString());
        formData.append("count", editedMovie.count.toString());
        formData.append("rating", editedMovie.rating.toString());

            fetch("http://localhost/movies/edit_movie.php", {
                method: "POST",
                body: formData
            })
                .then(() => refreshMovies());

        refreshMovies();
    }

    function deleteMovie(movieId: number){
        console.log("Deleting movie with id: ", movieId);
        fetch(`http://localhost/movies/delete_movie.php?id=${movieId}`)
            .then(() => refreshMovies());
        // setMovies(prevMovies => prevMovies.filter(movie => movie.id !== movieId));
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
                    <MovieList movies={movies} directors={directors} editMovie={editMovie} deleteMovie={deleteMovie}/>
                </Card>
            </div>
        </>

    );
}

export default App;
