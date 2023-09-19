fetch("./index.php?width=1200", {
    method: "POST"
})
.then(response => console.log(response.json()))
.then(data => {}
    
    // document.querySelector("img").src = data;
);
// .then(blob => document.querySelector("img").src = URL.createObjectURL(blob));