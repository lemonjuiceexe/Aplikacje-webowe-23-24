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
                <li key={movie.id} className="py-4 flex flex-row justify-evenly">
                    <div className="ml-3">
                        <p className="text-sm font-medium text-gray-900">
                            {movie.title}
                        </p>
                        <p className="text-sm text-gray-500">
                            {movie.year}
                        </p>
                    </div>
                    <div>
                        <p className="text-sm text-gray-900">
                            {movie.length}
                        </p>
                    </div>
                    <div>
                        <p className="text-sm text-gray-900">
                            &#9733;
                            {movie.rating}
                        </p>
                    </div>
                </li>
            ))}
        </ul>
    )
}