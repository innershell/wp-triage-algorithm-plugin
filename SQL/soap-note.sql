SELECT tUA.*,
    tC.choice as choice, 
    tC.is_correct as choice_correct,
    tC.assessment as assessment, 
    tC.plan as plan,
    tQ.question as question, 
    tQ.qtype as qtype, 
    tQ.soap_type as soap_type
FROM JHVLIRmkchained_user_answers tUA
JOIN JHVLIRmkchained_questions tQ ON tQ.id = tUA.question_id
LEFT JOIN JHVLIRmkchained_choices tC ON tC.id = tUA.answer
WHERE tUA.completion_id=463 ORDER BY tUA.ID