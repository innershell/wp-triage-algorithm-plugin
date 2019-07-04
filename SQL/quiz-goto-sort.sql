SELECT Quiz.title, Question.question, Choice.choice, 
    Quiz.id as quiz_id, 
    Question.id as question_id, 
    Choice.id as choice_id, 
    Choice.goto
FROM JHVLIRmkchained_quizzes Quiz,
    JHVLIRmkchained_questions Question,
    JHVLIRmkchained_choices Choice
WHERE Quiz.id = 9
AND Question.quiz_id = Quiz.id
AND Choice.question_id = Question.id
ORDER BY Question.sort_order, Choice.id