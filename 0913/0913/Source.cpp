#include<iostream>
int main()
{
	int start, stop;
	std::cin >> start >> stop;
	for ( stop=stop-1; stop > start; --stop)
	{
		
		std::cout << stop <<std::endl;
	}
	std::cin >> start;
	return 0;
}