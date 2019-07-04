SELECT Q.id as "Quiz ID", 
    UA.id as "User Answer ID", Q.title, UA.answer 
FROM JHVLIRmkchained_questions as Q, 
    JHVLIRmkchained_user_answers as UA 
WHERE Q.id = 80 
AND UA.question_id = Q.id 
AND UA.id > 797
