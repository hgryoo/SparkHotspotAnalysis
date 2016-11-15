#include <iostream>
#include <fstream>
#include <string>
#include <vector>

#define HOTSPOTNUM 50

using namespace std;

int main(int argc, char* argv[]) {

	if (argc != 3) {
		cout << "INPUT 2 File Name!!" << endl;
		return 1;
	}

	string baseName(argv[1]);
	string heuName(argv[2]);
	ifstream base(baseName);
	ifstream heuristic(heuName);

	vector<string> tmpStr;
	string buf;

	for (int i = 0; i < HOTSPOTNUM; i++) {
		getline(base, buf);
		tmpStr.push_back(buf);
	}
	
	int score = 0;
	for (int i = 0; i < HOTSPOTNUM; i++) {
		getline(heuristic, buf);
		for (vector<string>::iterator it = tmpStr.begin(); it != tmpStr.end(); it++) {
			
			size_t bColon = it->find_first_of(":");
			size_t cColon = buf.find_first_of(":");

			string baseTmp = it->substr(0, bColon);
			string compTmp = buf.substr(0, cColon);
			
			if (compTmp.compare(baseTmp) == 0) {
				score++;
				break;
			}
			
			if (buf.compare(*it) == 0) {
				score++;
				break;
			}
		}
	}

	
	getline(base, buf);
	int baseTime = stoi(buf);
	getline(heuristic, buf);
	int heuTime = stoi(buf);
	

	base.close();
	heuristic.close();

	cout << "base : " << argv[1] << endl;
	cout << "heuristic : " << argv[2] << endl;
	cout << "Score : " << score << "/" << HOTSPOTNUM <<endl;
	cout << "executionTime : " << baseTime << "ms VS " << heuTime << "ms" << endl;

	return 0;
}
