SELECT 
    tCOM.id as completed_id, 
    tQUIZ.title as topic_name, 
    tCOM.datetime as response_date, 
    tUA.answer_text as study_id, 
    tCOM.snapshot as soap_note
FROM 
	pEbhDRZAchained_quizzes tQUIZ,
	pEbhDRZAchained_completed tCOM,
    pEbhDRZAchained_questions tQUES,
    pEbhDRZAchained_user_answers tUA
WHERE tQUIZ.id = tCOM.quiz_id 
AND tQUIZ.id = tQUES.quiz_id
AND tQUES.id = tUA.question_id
AND tCOM.id = tUA.completion_id
AND tQUES.sort_order = 1
ORDER BY tCOM.datetime DESC
LIMIT 1,2