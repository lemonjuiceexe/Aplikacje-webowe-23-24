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

const movies: Movie[] = [
    {
        id: 1,
        title: "The Shawshank Redemption",
        year: 1994,
        length: "2:22:21",
        director_id: 1,
        count: 8,
        rating: 4.5
    },
    {
        id: 2,
        title: "The Godfather",
        year: 1972,
        length: "2:55:00",
        director_id: 2,
        count: 2,
        rating: 4.5
    },
    {
        id: 3,
        title: "The Dark Knight",
        year: 2008,
        length: "2:32:00",
        director_id: 3,
        count: 22,
        rating: 4
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
                        {/* edit and delete */}
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    {movies.map((movie) => (
                        <MovieItem movie={movie} editing={false}/>
                    ))}
                </tbody>
            </table>
        </div>
    )
}