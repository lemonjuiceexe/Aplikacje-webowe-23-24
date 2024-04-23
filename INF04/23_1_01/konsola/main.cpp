#include <iostream>

/**********************************************
nazwa funkcji: FindGreatestCommonDivisor
opis funkcji: funkcja znajduje najwiękzy wspólny dzielnik dwóch liczb przy użyciu algorytmu Euklidesa
parametry: (unsigned int) a - pierwsza z liczb, których NWD jest szukane
           (unsigned int) b - druga z liczb, których NWD jest szukane
zwracany typ i opis: (unsigned int) - największy wspólny dzielnik parametrów a i b
autor: [mój numer PESEL]
***********************************************/
int FindGreatestCommonDivisor(int a, int b){
    while (a != b){
        if (a > b){
            a -= b;
        }
        else{
            b -= a;
        }
    }

    return a;
}

int main(){
    int a, b;
    std::cout << "Podaj a i b:" << std::endl;
    std::cin >> a >> b;
    
    if(a <= 0 || b <= 0){
        std::cout << "Niepoprawne dane" << std::endl;
        return 0;
    }

    std::cout << "Największy wspólny dzielnik to " << FindGreatestCommonDivisor(a, b) << std::endl;

    return 0;
}
