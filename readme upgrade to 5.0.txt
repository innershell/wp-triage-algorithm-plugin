// These are the changes that need to be done when upgrading the plugin from 4.0 to 5.0.

UGPRADE CONFIG
--------------
For checkbox question, if there ARE follow-up questions in the chain:
A. Answers that do not need follow-up must directly specify the next question
   to ask outside of the current "question set".
B. Follow-up answers must also directly specify the next question to ask
   outside of the current "question set".
C. Remove periods from other cormorbidities so that the "None" answer will work correctly.

Disable storing media organized by month and year-based folders.


SCENARIO
--------
    QUESTION A
        answer A1->FA1
        answer A2->B
        answer A3->FA3
        QUESTION FA1->B
        QUESTION FA3->B
    QUESTION B->Finalize

