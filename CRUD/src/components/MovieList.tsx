import MovieItem from "./MovieItem.tsx";
import {Director, Movie} from "../App.tsx";

export default function MovieList({movies, directors, editMovie, deleteMovie}:
{movies: Movie[], directors: Director[], editMovie: (editedMovie: Movie) => void, deleteMovie: (movieId: number) => void}) {
    const columns: string[] = ["id", "Title", "Director", "Length", "Rating", "Count"];
    return (
        <div className={"overflow-x-auto"}>
            <table className="table">
                <thead>
                    <tr>
                        {columns.map((column) => (<th>{column}</th>))}
                        {/* edit and delete columns */}
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {movies.map((movie, index) => (
                        <MovieItem
                            key={index}
                            movie={movie}
                            directors={directors}
                            editing={false}
                            editMovie={editMovie}
                            deleteMovie={deleteMovie}
                        />
                    ))}
                </tbody>
            </table>
        </div>
    )
}