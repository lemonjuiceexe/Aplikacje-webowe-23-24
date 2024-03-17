import {ChangeEvent, forwardRef,  useRef, useState} from "react";
import {NewMovie} from "../App.tsx";

interface NewMovieDialogProps {
    addNewMovie: (movie: NewMovie) => void;
}

const NewMovieDialog = forwardRef<HTMLDialogElement, NewMovieDialogProps>(
    function NewMovieDialog({addNewMovie}: NewMovieDialogProps, ref) {
        const titleRef = useRef<HTMLInputElement>(null);
        const yearRef = useRef<HTMLInputElement>(null);
        const directorRef = useRef<HTMLInputElement>(null);
        const countRef = useRef<HTMLInputElement>(null);
        const [length, setLength] = useState([0, 0, 0]);
        const [rating, setRating] = useState(0);

        function lengthChangeHandler(event: ChangeEvent<HTMLInputElement>, index: number){
            const newLength = [...length];
            newLength[index] = parseInt(event.target.value);
            setLength(newLength);
        }
        function addNewMovieHandler() {
            const newMovie: NewMovie = {
                title: titleRef.current!.value,
                year: parseInt(yearRef.current!.value),
                director: directorRef.current!.value,
                rating: rating,
                length: length.join(":"),
                count: parseInt(countRef.current!.value)
            };
            addNewMovie(newMovie);

            titleRef.current!.value = "";
            yearRef.current!.value = "";
            directorRef.current!.value = "";
            countRef.current!.value = "";
            setLength([0, 0, 0]);
            setRating(0);
        }

        return (
            <dialog ref={ref}
                    className={"absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 rounded-lg p-6 w-4/12"}>
                <h2 className={"text-2xl font-bold leading-9 text-gray-900 sm:text-3xl sm:leading-10 mb-6 p-4"}>Add a
                    new movie</h2>
                <form className={"flex flex-col mb-8"}>
                    <div className={"flex flex-row"}>
                        <label className="form-control w-full max-w-xs px-2">
                            <div className="label">
                                <span className="label-text">Movie title</span>
                            </div>
                            <input type="text" placeholder="The Godfather" ref={titleRef}
                                   className="input input-bordered w-full max-w-xs"/>
                        </label>
                        <label className="form-control w-full max-w-xs px-2">
                            <div className="label">
                                <span className="label-text">Release year</span>
                            </div>
                            <input type="number" placeholder="1972" min="1900" max="2030" ref={yearRef}
                                   className="input input-bordered w-20 max-w-xs"/>
                        </label>
                    </div>
                    <div className={"flex flex-row"}>
                        <label className="form-control w-full max-w-xs px-2">
                            <div className="label">
                                <span className="label-text">Director</span>
                            </div>
                            <input type="text" placeholder="Francis Ford Coppola" ref={directorRef}
                                   className="input input-bordered  w-full max-w-xs"/>
                        </label>
                        <label className="form-control w-full max-w-xs px-2">
                            <div className="label">
                                <span className="label-text">Rating</span>
                            </div>
                            <div className="rating rating-md mt-1 h-8 flex justify-start items-center">
                                {[1, 2, 3, 4, 5].map((star) => (
                                    <input type="radio"
                                           name={`rating`}
                                           className={`mask mask-star-2 bg-secondary`}
                                           onInput={() => setRating(star)}
                                           key={star}
                                           {...(star === rating ? {checked: true} : {})}
                                    />
                                ))}
                            </div>
                        </label>
                    </div>
                    <div className={"flex flex-row"}>
                        <label className="form-control w-full max-w-xs px-2">
                            <div className="label">
                                <span className="label-text">Length</span>
                            </div>
                            <div className="flex items-center">
                                {[0, 1, 2].map((index) => (
                                    <div key={index}>
                                        <input className={"input input-bordered input-sm mx-1 w-10"}
                                               type={"number"} placeholder={index === 0 ? "2" : "55"}
                                               min={"0"} max={index === 0 ? "99" : "59"}
                                                  value={length[index]}
                                               onChange={(event) =>
                                                   lengthChangeHandler(event, index)
                                               }
                                        />
                                        {index !== 2 && <span>:</span>}
                                    </div>
                                ))}
                            </div>
                        </label>
                        <label className="form-control w-full max-w-xs px-2">
                            <div className="label">
                                <span className="label-text">Count</span>
                            </div>
                            <input type="number" placeholder="1" min="1" ref={countRef}
                                   className="input input-bordered input-sm w-12 max-w-xs"/>
                        </label>
                    </div>
                </form>
                <form method={"dialog"} className={"flex flex-row justify-around"}>
                    <button className={"btn btn-error w-32 text-white"}>Cancel</button>
                    <button className={"btn btn-success w-32 text-white"}
                            onClick={addNewMovieHandler}
                    >Save
                    </button>
                </form>
            </dialog>
        );
    });

export default NewMovieDialog;