export default function Card({children}: {children: React.ReactNode}) {
    return (
        <div className="
			bg-white
			drop-shadow-lg
			overflow-hidden
			rounded-bl
			p-3
			bg-gray-100
		">
            {children}
        </div>
    );
}