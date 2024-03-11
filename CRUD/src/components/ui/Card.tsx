export default function Card(props){
    return (
        <div className="
			bg-white
			drop-shadow-lg
			overflow-hidden
			rounded-bl
			p-6
			bg-gray-100
		">
            {props.children}
        </div>
    );
}