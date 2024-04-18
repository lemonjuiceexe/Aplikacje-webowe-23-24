import {Director, Movie} from "../App.tsx";
import {useEffect, useRef, useState} from "react";

export default function MovieItem(props: {
    movie: Movie, directors: Director[], editing: boolean,
    editMovie: (editedMovie: Movie) => void, deleteMovie: (movieId: number) => void}
){
    const [editing, setEditing] = useState(props.editing);
    const [director, setDirector] = useState(
        props.directors.filter(director => director.id === props.movie.director_id)[0]
    );
    const [movieLength, setMovieLength] = useState(props.movie.length.split(":"));

    const [movieTitle, setMovieTitle] = useState(props.movie.title);

    const movieTitleRef = useRef<HTMLInputElement>(null);
    const movieYearRef = useRef<HTMLInputElement>(null);
    const movieDirectorRef = useRef<HTMLSelectElement>(null);
    const movieLengthHourRef = useRef<HTMLInputElement>(null);
    const movieLengthMinuteRef = useRef<HTMLInputElement>(null);
    const movieLengthSecondRef = useRef<HTMLInputElement>(null);
    const movieRatingRef = useRef<HTMLInputElement>(null);
    const movieCountRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        refreshDirector();
    }, [props.movie]);


    function refreshDirector(){
        setDirector(props.directors.filter(director => director.id === props.movie.director_id)[0]);
    }
    function editClickHandler() {
        if (editing) {
            editMovie();
        }
        setEditing(prev => !prev);
        refreshDirector();
    }
    function deleteClickHandler() {
        props.deleteMovie(props.movie.id);
    }
    function editMovie() {
        const year = movieYearRef.current?.value ? parseInt(movieYearRef.current.value) : props.movie.year;
        const director = movieDirectorRef.current?.value ? parseInt(movieDirectorRef.current.value) : props.movie.director_id;
        const movieLength = `${movieLengthHourRef.current!.value}:${movieLengthMinuteRef.current!.value}:${movieLengthSecondRef.current!.value}`;
        const movieRating = movieRatingRef.current?.value ? parseInt(movieRatingRef.current.value) : props.movie.rating;
        const movieCount = movieCountRef.current?.value ? parseInt(movieCountRef.current.value) : props.movie.count;

        const editedMovie: Movie = {
            id: props.movie.id,
            title: movieTitleRef.current?.value || props.movie.title,
            year: year,
            director_id: director,
            length: movieLength,
            rating: movieRating,
            count: movieCount
        };

        props.editMovie(editedMovie);
    }
    function movieLengthEditHandler(event: React.ChangeEvent<HTMLInputElement>, index: number) {
        console.log(event.target.value);
        const newLength = [...movieLength];
        newLength[index] = event.target.value;
        setMovieLength(newLength);
        const editedMovie: Movie = {...props.movie};
        editedMovie.length = newLength.join(":");
        props.editMovie(editedMovie);
    }

    return (
        <tr key={props.movie.id}>
            <td>
                {props.movie.id}
            </td>
            <td>
                {!editing ?
                    (<><span className={"font-bold"}>{props.movie.title}</span> ({props.movie.year})</>) :
                    (<>
                        <input className={"input input-bordered input-sm mx-1 w-40"}
                               type={"text"}
                               placeholder={props.movie.title}
                               defaultValue={props.movie.title}
                               ref={movieTitleRef}
                        />
                        <input className={"input input-bordered input-sm mx-1 w-14"}
                               type={"number"}
                               min={"1900"} max={"2024"}
                               placeholder={props.movie.year.toString()}
                               defaultValue={props.movie.year.toString()}
                               ref={movieYearRef}
                        />
                    </>)
                }
            </td>
            <td>
                {!editing ? (director.name) : (
                    <select className={"select select-bordered select-sm w-48"}
                            defaultValue={director.id.toString()}
                            ref={movieDirectorRef}
                    >
                        {props.directors.map((director, index) => (
                            <option value={director.id} key={index}>{director.name}</option>
                        ))})
                    </select>
                )}
            </td>
            <td>
                {!editing ? (props.movie.length) : (
                    <div className={"flex items-center"}>
                        {[0, 1, 2].map((index) => (
                            <div key={index}>
                            <input className={"input input-bordered input-sm mx-1 w-12"}
                                   type={"number"}
                                   min={"0"} max={index === 0 ? "99": "59"}
                                   defaultValue={movieLength[index]}
                                   ref={[movieLengthHourRef, movieLengthMinuteRef, movieLengthSecondRef][index]}
                            />
                            {index !== 2 && <span>:</span>}
                            </div>
                        ))}
                    </div>
                )}
            </td>
            <td>
                {!editing ? (<span>&#9733;{props.movie.rating}</span>) : (
                    <div className="rating">
                        {[1, 2, 3, 4, 5].map((star) => (
                            <input type="radio"
                                   key={star}
                                   name={`rating-${props.movie.id}`}
                                   className={`mask mask-star-2 bg-secondary`}
                                   defaultChecked={star === props.movie.rating}
                                   ref={movieRatingRef}
                            />
                        ))}
                    </div>
                )}
            </td>
            <td>
            {!editing ? (props.movie.count) : (
                    <input className={"input input-bordered input-sm w-14"}
                           type={"number"}
                           min={"0"}
                           defaultValue={props.movie.count}
                           ref={movieCountRef}
                    />
                )}
            </td>
            <td>
                <button onClick={editClickHandler}
                        className={`btn btn-sm ${!editing ? "btn-warning" : "btn-success text-white"}`}>{!editing ? "Edit" : "Save"}
                </button>
            </td>
            <td>
                <button onClick={deleteClickHandler}
                        className={"btn btn-sm btn-error text-white"}>Delete</button>
            </td>
        </tr>
    );
}