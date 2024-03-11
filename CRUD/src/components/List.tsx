const movies = [
    {
        id: 1,
        title: "The Shawshank Redemption",
        year: 1994,
        length: "2:22:21",
        director_id: 1,
        count: 8,
        rating: 4.5
    }
];

export default function List() {
    return (
        <ul role="list" className="divide-y divide-gray-100">
            {movies.map((movie) => (
               <p>{movie.title}</p>
            ))}
        </ul>
    )
}