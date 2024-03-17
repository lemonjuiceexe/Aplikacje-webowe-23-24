import MovieItem from "./MovieItem.tsx";

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

const movies: Movie[] = [
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

export default function MovieList() {
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
                    {movies.map((movie) => (
                        <MovieItem
                            key={movie.id}
                            movie={movie}
                            directors={directors}
                            editing={false}/>
                    ))}
                </tbody>
            </table>
        </div>
    )
}