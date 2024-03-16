import {Movie} from "./MovieList.tsx";
import {useState} from "react";

export default function MovieItem(props: {movie: Movie, editing: boolean}){
    const [editing, setEditing] = useState(props.editing);
    const [movie, setMovie] = useState(props.movie);

    function editClickHandler() {
        setEditing(prev => !prev);
    }
    function movieEditHandler(event: React.ChangeEvent<HTMLInputElement>, keyToEdit: string) {
        setMovie(prev => {
            return {
                ...prev,
                [keyToEdit]: event.target.value
            };
        });
    }

    return (
        <tr key={movie.id}>
            <td>
                {movie.id}
            </td>
            <td>
                {!editing ?
                    (<><span className={"font-bold"}>{movie.title}</span> ({movie.year})</>) :
                    (<>
                        <input className={"input input-sm mx-1"}
                               type={"text"}
                               value={movie.title}
                               onChange={(event: React.ChangeEvent<HTMLInputElement>) =>
                                   movieEditHandler(event, "title")
                               }
                        />
                        <input className={"input input-sm mx-1"}
                               type={"number"}
                               min={"1900"} max={"2024"}
                               value={movie.year}
                               onChange={(event: React.ChangeEvent<HTMLInputElement>) =>
                                  movieEditHandler(event, "year")
                               }
                        />
                    </>)
                }
            </td>
            <td>
                {movie.director_id}
            </td>
            <td>
                {movie.length}
            </td>
            <td>
                &#9733;
                {movie.rating}
            </td>
            <td>
                {movie.count}
            </td>
            <td>
                <button
                    onClick={editClickHandler}
                    className={"btn btn-sm btn-warning"}>{!editing ? "Edit" : "Save"}
                </button>
            </td>
            <td>
                <button className={"btn btn-sm btn-error text-white"}>Delete</button>
            </td>
        </tr>
    );
}