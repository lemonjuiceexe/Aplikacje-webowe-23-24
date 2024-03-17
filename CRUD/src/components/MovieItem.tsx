import {Director, Movie} from "../App.tsx";
import {useState} from "react";

export default function MovieItem(props: {movie: Movie, directors: Director[], editing: boolean}){
    const [editing, setEditing] = useState(props.editing);
    const [movie, setMovie] = useState(props.movie);
    const [director, setDirector] = useState(
        props.directors.filter(director => director.id === movie.director_id)[0]
    );
    const [movieLength, setMovieLength] = useState(movie.length.split(":"));

    function editClickHandler() {
        setEditing(prev => !prev);
    }
    function movieEditHandler(event: React.ChangeEvent<HTMLInputElement> | React.ChangeEvent<HTMLSelectElement>, keyToEdit: string, value?: number | string) {
        if(keyToEdit === "director_id") {
            const director = props.directors.filter(director => director.id === parseInt(event.target.value))[0];
            setDirector(director);
        }
        setMovie((prev: Movie) => {
            return {
                ...prev,
                [keyToEdit]: value ? value : event.target.value
            };
        });
    }
    function movieLengthEditHandler(event: React.ChangeEvent<HTMLInputElement>, index: number) {
        console.log(event.target.value);
        const newLength = [...movieLength];
        newLength[index] = event.target.value;
        setMovieLength(newLength);
        setMovie((prev: Movie) => {
            return {
                ...prev,
                length: newLength.join(":")
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
                        <input className={"input input-bordered input-sm mx-1 w-40"}
                               type={"text"}
                               value={movie.title}
                               onChange={(event: React.ChangeEvent<HTMLInputElement>) =>
                                   movieEditHandler(event, "title")
                               }
                        />
                        <input className={"input input-bordered input-sm mx-1 w-14"}
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
                {!editing ? (director.name) : (
                    <select className={"select select-bordered select-sm w-48"}
                            value={director.id}
                            onChange={(event: React.ChangeEvent<HTMLSelectElement>) =>
                                movieEditHandler(event, "director_id")
                            }
                    >
                        {props.directors.map(director => (
                            <option value={director.id}>{director.name}</option>
                        ))})
                    </select>
                )}
            </td>
            <td>
                {!editing ? (movie.length) : (
                    <div className={"flex items-center"}>
                        {[0, 1, 2].map((index) => (
                            <div key={index}>
                            <input className={"input input-bordered input-sm mx-1 w-10"}
                                   type={"number"}
                                   min={"0"} max={index === 0 ? "99": "59"}
                                   value={movieLength[index]}
                                   onInput={(event: React.ChangeEvent<HTMLInputElement>) =>
                                       movieLengthEditHandler(event, index)
                                   }
                            />
                            {index !== 2 && <span>:</span>}
                            </div>
                        ))}
                    </div>
                )}
            </td>
            <td>
                {!editing ? (<span>&#9733;{movie.rating}</span>) : (
                    <div className="rating">
                        {[1, 2, 3, 4, 5].map((star) => (
                            <input type="radio"
                                   name={`rating-${movie.id}`}
                                   className={`mask mask-star-2 bg-secondary`}
                                   {...(star === movie.rating && {checked: true})}
                                   onChange={(event: React.ChangeEvent<HTMLInputElement>) =>
                                       movieEditHandler(event, "rating", star)
                                   }
                            />
                        ))}
                    </div>
                )}
            </td>
            <td>
            {!editing ? (movie.count) : (
                    <input className={"input input-bordered input-sm w-14"}
                           type={"number"}
                           min={"0"}
                           value={movie.count}
                           onChange={(event: React.ChangeEvent<HTMLInputElement>) =>
                               movieEditHandler(event, "count")
                           }
                    />
                )}
            </td>
            <td>
                <button
                    onClick={editClickHandler}
                    className={`btn btn-sm ${!editing ? "btn-warning" : "btn-success text-white"}`}>{!editing ? "Edit" : "Save"}
                </button>
            </td>
            <td>
                <button className={"btn btn-sm btn-error text-white"}>Delete</button>
            </td>
        </tr>
    );
}