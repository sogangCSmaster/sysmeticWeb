#include <stdio.h>
#include <stdlib.h>
#include <string.h>

#include "SciSecuX.h"

int main(int argc, char *argv[])
{
	char Type[32] = {0,};
	char Iv[17] = {0,};
	char Data[BUFFSIZE] = {0,};
    char HashData[BUFFSIZE] = {0,};
	char ResEnc[BUFFSIZE] = {0,};
	char ResDec[BUFFSIZE] = {0,};
	char szTmp[128] = {0,};
	int  nJob;
	int  nFlag;

    char *pEncData;
    char *pDecData;

    if (argc < 2) {
		printf("[Manual] SciSecuX SEED/AES/HMAC\n");
        return 0 ;
    }

	sprintf(Type, "%s", argv[1]);
	nJob = atoi(argv[2]);
	nFlag = atoi(argv[3]);

    if(!strcmp(Type, TYPE_SEED)) {
		if (argc == 6) {
			sprintf(szTmp, "%s", argv[4]);

			if(strlen(szTmp) < 16) {
				memcpy(Iv, szTmp, strlen(szTmp));
				memset(Iv+strlen(szTmp), 0x30, 16-strlen(szTmp));
				Iv[16] = 0x00;
			}
			else if(strlen(szTmp) > 16) {
				memcpy(Iv, szTmp+(strlen(szTmp)-16), 16);
				Iv[16] = 0x00;
			}
			else {
				strcpy(Iv, szTmp);
			}

			sprintf(Data, "%s", argv[5]);
		}
		else if (argc == 5) {
			sprintf(Data, "%s", argv[4]);
		}
		else {
			printf("[Manual] Encript => SciSecuX SEED 1 0 Iv Data\n");
			printf("[Manual] Decrypt => SciSecuX SEED 2 0 Iv Data\n");
			return 0 ;
		}

		if(nJob == JOB_ENC) {
			SeedEnc(ResEnc, Data, strlen(Data), nFlag, Iv, EncKey);
			printf("%s", ResEnc);
		}
		else {
			SeedDec(ResDec, Data, strlen(Data), nFlag, Iv, EncKey);
			printf("%s", ResDec);
		}
    } else if(!strcmp(Type, TYPE_AES)) {
		if (argc < 4) {
			printf("[Manual] Encript => SciSecuX AES 1 Data\n");
			printf("[Manual] Decrypt => SciSecuX AES 2 Data\n");
			return 0 ;
		}

		nJob = atoi(argv[2]);
		sprintf(Data, "%s", argv[3]);

		if(nJob == JOB_ENC) {
			pEncData = Encryption(Data);
			printf("%s", pEncData);

			if(pEncData) free(pEncData);
		}
		else {
			pDecData = Decryption(Data);
			printf("%s", pDecData);

			if(pDecData) free(pDecData);
		}
    } else if(!strcmp(Type, TYPE_HMAC)) {
		if (argc < 4) {
			printf("[Manual] Encript => SciSecuX HMAC Job Flag Data\n");
			printf("[Manual] Compare => SciSecuX HMAC Job HexaData Data\n");
			return 0 ;
		}

		nJob = atoi(argv[2]);

		if(nJob == JOB_ENC) {
			nFlag = atoi(argv[3]);
			sprintf(Data, "%s", argv[4]);

			pEncData = HMacEncript((unsigned char*)Data, strlen(Data), nFlag, EncKey);
			printf("%s", pEncData);

			if(pEncData) free(pEncData);
		}
		else {
			sprintf(Data, "%s", argv[3]);
			sprintf(HashData, "%s", argv[4]);

			printf("%d", HMacCompare(HashData, Data, EncKey));
		}
    }

   return 0;
}
