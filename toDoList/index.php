<?php
include "includes/functions.php";
include "includes/header.php";

if (empty($_SESSION)) {
    header('Location: login.php');
}

$name = $_SESSION['name'];
$user_id =  $_SESSION['id'];
$data = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $description = test_input($_POST["description"]);


    if (empty($description)) {
        $error["description"] = "Description cannot be empty";
    }

    if (empty($error)) {
        $stmt = $con->prepare("INSERT INTO task (user_id, description) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $description);
        $stmt->execute();
        $stmt->close();
    }

}

if ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $stmt = $con->prepare("DELETE FROM task WHERE id=? AND user_id=?");
    $stmt->bind_param("ii",$id, $user_id);
    $stmt->execute();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($_GET['complete'])) {
    $id = intval($_GET['complete']);
    $status = intval($_GET['status']);
    if ($status === 1 || $status === 0) {
        $stmt = $con->prepare("UPDATE task SET is_completed=? WHERE id=? AND user_id=?");
        $stmt->bind_param("iii",$status, $id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
    
}

$stmt = $con->prepare("SELECT * FROM task WHERE user_id=?");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
    $data[] = $row;
    }
}
$con->close();

?>

<nav class="nav-extended">
    <div class="nav-wrapper teal lighten-2">
      <span class="brand-logo center">To Do List</span>
      <ul id="nav-mobile" class="left hide-on-med-and-down">
        <li><a>Hello <?= $name ?><span data-badge-caption="task(s) pending" class="new badge red lighten-3"><?= array_count_values(array_column($data, 'is_completed'))[0] ?? "0"; ?></span></a></li>
      </ul>
    <ul id="nav-mobile" class="right hide-on-med-and-down">
        <li><a href="logout.php">Logout</a></li>
    </ul>
    </div>
    <div class="nav-content teal lighten-2 hide-on-large-only">
        <ul class="tabs tabs-transparent">
            <li class="tab"><a>Hello <?= $name ?><span data-badge-caption="task(s) pending" class="new badge red lighten-3"><?= array_count_values(array_column($data, 'is_completed'))[0]; ?></span></a></li>
            <li class="tab"><a href="logout.php">Logout</a></li>
        </ul>      
    </div>
</nav>


<div class="container">
    <form id="task" action="index.php" method="post">
    <div class="row">
        <div class="input-field col s10 m11">
            <textarea id="description" name="description" class="materialize-textarea"></textarea>
            <label for="description">Add New Task</label>
            <span class="helper-text"><?= $error["description"] ?? ""; ?></span>
        </div>
        <div class="input-field col s2 m1">
            <button type="submit" class="btn-floating btn-medium waves-effect waves-light"><i class="material-icons">add</i></button>
        </div>
    </div>
    </form>
    <ul class="collection with-header">
        <li class="collection-header"><h4>Tasks To Do</h4></li>
        <?php if ($data): ?>
            <?php foreach($data as $row): ?>
                <?php if ($row['is_completed']): ?>
                <li class="collection-item is_completed"><div><del><?= $row['description']; ?></del>
                <a href="index.php?delete=<?= h(u($row['id'])); ?>" class="secondary-content"><i class="material-icons">delete</i></a>
                <a href="index.php?complete=<?= h(u($row['id'])); ?>&status=0" class="secondary-content"><i class="material-icons">check_box</i></a>
                <?php else: ?>
                <li class="collection-item"><div><?= $row['description']; ?>
                <a href="index.php?delete=<?= h(u($row['id'])); ?>" class="secondary-content"><i class="material-icons">delete</i></a>
                <a href="index.php?complete=<?= h(u($row['id'])); ?>&status=1" class="secondary-content"><i class="material-icons">check_box_outline_blank</i></a>
                <?php endif ?>
            </div></li>
            <?php endforeach ?>
        <?php else: ?>
            <p>No Tasks Found</p>
        <?php endif ?>
    </ul>
</div>

<?php
include "includes/footer.php";
?>
