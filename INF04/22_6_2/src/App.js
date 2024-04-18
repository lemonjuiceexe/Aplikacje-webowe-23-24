import logo from './logo.svg';
import './App.css';
import 'bootstrap/dist/css/bootstrap.css';
import {useState, useRef} from "react";

function App() {
  const [courses, setCourses] = useState(["Programowanie w C#", "Angular dla początkujących", "Kurs Django"]);

  const nameRef = useRef();
  const courseRef = useRef();

    function submitHandler(e) {
        e.preventDefault();
        const name = nameRef.current.value;
        const courseId = courseRef.current.value;
        const courseCorrect = courseId > 0 && courseId <= courses.length;

        console.log(name);
        console.log(courseCorrect ? courses[courseId - 1] : "Nieprawidłowy numer kursu");
    }

    return (
    <div className="App container p-5">
      <h2>Liczba kursów: {courses.length}</h2>
      <ol className="my-4">
        {courses.map((course, index) => {
          return(
              <li key={index}>
                  <p>{course}</p>
              </li>
          );
        })}
      </ol>
      <form onSubmit={submitHandler}>
          <div className="form-group my-2">
            <label htmlFor="name">Imię i nazwisko:</label>
            <input className="form-control" type="text" id="name" name="name" ref={nameRef}></input>
          </div>
          <div className="form-group my-2">
            <label htmlFor="courseId">Numer kursu:</label>
            <input className="form-control" type="number" id="courseId" name="courseId" ref={courseRef}></input>
          </div>
          <button type="submit"
                  className="btn btn-primary form-control w-auto my-2">
              Zapisz do kursu
          </button>
      </form>
    </div>
  );
}

export default App;
