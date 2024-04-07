#include<iostream>

class Array {
    public:
        int array[10];

        /********************************************************
        * nazwa funkcji: selection_sort
        * parametry wejściowe: brak
        * wartość zwracana: brak
        * autor: Franciszek Niwicki
        * ****************************************************/
        void selection_sort(){
            while(!is_sorted()){
                for(int i = 0; i < 10; ++i){
                    int max_index = find_max_index(i);
                    std::swap(this->array[i], this->array[max_index]);
                }
            }
        }
        /********************************************************
        * nazwa funkcji: print_array
        * parametry wejściowe: brak
        * wartość zwracana: brak
        * autor: Franciszek Niwicki
        * ****************************************************/
        void print_array(){
            for(int i = 0; i < 10; ++i){
                std::cout << this->array[i] << " ";
            }
            std::cout << std::endl;
        }

    private:
        /********************************************************
        * nazwa funkcji: find_max_index
        * parametry wejściowe: 
        *   - (int) start_index - indeks tablicy od którego należy rozpocząć wyszukiwanie największej wartości
        * wartość zwracana: (int) indeks największej wartości w tablicy
        * autor: Franciszek Niwicki
        * ****************************************************/
        int find_max_index(int start_index){
            int max_value = this->array[9];
            int max_index = 9;
            for(int i = start_index; i < 10; ++i){
                max_index = (max_value > this->array[i]) ?
                    max_index : i;
                max_value = (max_index != i) ? 
                    max_value : this->array[i];
            }

            return max_index;
        }
        /********************************************************
        * nazwa funkcji: is_sorted
        * parametry wejściowe: brak
        * wartość zwracana: (bool) prawda jeśli tablica jest posortowana malejąco, w innym wypadku fałsz
        * autor: Franciszek Niwicki
        * ****************************************************/
        bool is_sorted(){
            for(int i = 0; i < 9; ++i){
                if(this->array[i] < this->array[i + 1])
                    return false;
            }
            return true;
        }
};

/********************************************************
* nazwa funkcji: main
* parametry wejściowe: brak
* wartość zwracana: (int) kod zakończenia programu. 0 - poprawne zakończenie.
* autor: Franciszek Niwicki
* ****************************************************/
int main(){
    Array sorting;

    std::cout << "Podaj 10 liczb całkowitych do posortowania." << std::endl;
    for(int i = 0; i < 10; ++i)
        std::cin >> sorting.array[i];

    sorting.selection_sort();
    sorting.print_array();

    return 0;
}