<?php 
    require $_SERVER['DOCUMENT_ROOT']."/utils/db.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_course'])) {
        $count = count($_FILES['images']['name']); 
        if($count < 1) {
            $errors[] = 'Вы не загрузили доказательства';
        }

        if (count($_FILES['images']['name']) > 0) {
            $filename = '';

            for($i=0; $i < $count; $i++){
                if(!empty($_FILES['images']['name'][$i])){            
                    $_FILES['image']['name'] = $_FILES['images']['name'][$i];
                    $_FILES['image']['type'] = $_FILES['images']['type'][$i];
                    $_FILES['image']['tmp_name'] = $_FILES['images']['tmp_name'][$i];
                    $_FILES['image']['error'] = $_FILES['images']['error'][$i];
                    $_FILES['image']['size'] = $_FILES['images']['size'][$i];
            
                    $target_dir = "./uploads/";
                    $target_file = $target_dir . basename($_FILES["image"]["name"]);
                    $uploadOk = 1;
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                    $filename = basename(md5(uniqid(rand(), true)) . '.' . $imageFileType);
                    $dest_file = $target_dir . $filename;
                    
                    // Check if image file is a actual image or fake image
                    if(isset($_POST["create_course"])) {
                        $check = getimagesize($_FILES["image"]["tmp_name"]);
    
                        if($check !== false) {
                            $uploadOk = 1;
                        } else {
                            $errors[] =  "Это не изображение";
                            $uploadOk = 0;
                        }
                    }
    
                    // Check file size
                    if ($_FILES["image"]["size"] > 1024 * 1024 * 10) {
                        $errors[] =  "Файл слишком большой";
                        $uploadOk = 0;
                    }
    
                    // Allow certain file formats
                    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                        $errors[] =  "Доспускается только JPG, JPEG, PNG & GIF.";
                        $uploadOk = 0;
                    }
    
                    // Check if $uploadOk is set to 0 by an error
                    if ($uploadOk == 0) {
                        $errors[] = "Файл не был загружен";
                        // if everything is ok, try to upload file
                    } else {
                        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $dest_file)) {
                            $errors[] = "Sorry, there was an error uploading your file.";
                        }
                    }
                }    
            }

            $course_name = $_POST['course_name'];

            if((trim($course_name) == "")){
                $errors[] = "Введите название курса";
            }
            
            $rep_course = R::find("courses","name = ?", array($course_name));
            if($rep_course){
                $errors[] = "Курс с таким именем уже существует";
            }

            try{
                if(empty($errors)){
                    $course = R::dispense("courses");
                    $course->name = $course_name;
                    $course->img = $filename;
                    R::store($course);

                    $success[] = "Курс был создан";
                }
            } catch (\Throwable $th) {
                throw $th;
                die();                
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adding new course</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link type="text/css" rel="stylesheet" href="/assets/libs/img_upload/image-uploader.min.css">
</head>
<body>
    <div class="container col-5 my-4">
        <?php 
            if(!empty($errors)){ ?>
            <div class="col-12 bg-white py-1 mb-5">
                <h2 class="text-center text-danger">
                    <?php  
                      echo array_shift($errors);
                    ?>
                </h2>
            </div>
        <?php
            }
        ?>

        <?php 
            if(!empty($success)){ ?>
            <div class="col-12 bg-white py-1 mb-5">
                <h2 class="text-center text-success">
                    <?php  
                      echo array_shift($success);
                    ?>
                </h2>
            </div>
        <?php
            }
        ?>
        <h3 style="color:red;"></h3>
        <h2 class='text-center my-2'>Создать новый курс</h2>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" class="text-center" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" class="form-control" name="course_name" placeholder="Название курса">
            </div>

            <div class="form-group">
                <div class="gallery-create-images"></div>
            </div>

            <div class="form-group">
                <button class="btn btn-lg btn-success w-50" type="submit" name="create_course">Создать курс</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    <script type="text/javascript" src="/assets/libs/img_upload/image-uploader.min.js"></script>
    <script type="text/javascript" src="/assets/scripts/script.js"></script>
</body>
</html>