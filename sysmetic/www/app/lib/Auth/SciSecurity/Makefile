CC = cc

SECULIB  = -L. -lSCISecuX

BINDIR = ./

.SUFFIXES :.o .c 

TARGET	= SciSecuX

OBJMAIN = SciSecuX.c 

all :	$(TARGET) 

$(TARGET) :   $(OBJMAIN)
	$(CC)   -o $(BINDIR)$@ $(OBJMAIN) $(SECULIB)
	cp $(TARGET) ../
clean:
	rm -rf *.o $(TARGET) 
