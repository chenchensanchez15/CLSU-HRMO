<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Applicant Dashboard | CLSU HRMO</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
tailwind.config = {
    theme: {
        extend: {
            colors: {
                clsuGreen: '#0B6B3A',
                clsuGold: '#F2C94C'
            }
        }
    }
}
</script>
<style>
/* Custom dashboard styles */
body { margin:0; font-family: Arial, sans-serif; background:#f4f6f9; }

/* NAVBAR */
.navbar {
    background: #0B6B3A;
    padding: 15px 30px;
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.navbar a {
    color: #fff;
    margin-right: 20px;
    font-weight: bold;
    text-decoration: none;
}

/* LAYOUT */
.container { display: flex; padding: 30px; gap: 30px; flex-wrap: wrap; }

/* LEFT PANEL */
.left {
    width: 30%;
    min-width: 250px;
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
}
.profile-pic {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #ccc;
    margin: 0 auto 15px;
}
.left h3 { color: #0B6B3A; margin-bottom: 5px; }

/* RIGHT PANEL */
.right { width: 70%; min-width: 300px; }
.card { background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 20px; }
.card h3 { color: #0B6B3A; margin-bottom: 15px; }

table { width: 100%; border-collapse: collapse; }
th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }

.status { padding: 5px 10px; border-radius: 5px; color: #fff; font-size: 13px; }
.Pending { background: orange; }
.Approved { background: green; }
.Rejected { background: red; }

@media (max-width: 768px) {
    .container { flex-direction: column; }
    .left, .right { width: 100%; }
}
</style>
</head>
<body>

<!-- NAVBAR -->
<div class="navbar">
    <div>
        <a href="#">Personal</a>
        <a href="#">Trainings</a>
        <a href="#">Other Details</a>
    </div>
    <div>Applicant Dashboard</div>
</div>

<div class="container">

    <!-- LEFT PANEL -->
    <div class="left">
        <div class="profile-pic">
            <?php if(!empty($user['photo'])): ?>
                <img src="<?= base_url('uploads/' . esc($user['photo'])) ?>" alt="Profile" class="w-full h-full rounded-full">
            <?php endif; ?>
        </div>
        <h3><?= esc($user['first_name'] ?? 'No') ?> <?= esc($user['last_name'] ?? 'Name') ?></h3>
        <p><?= esc($user['email'] ?? 'noemail@example.com') ?></p>
        <p><?= esc($user['contact'] ?? 'N/A') ?></p>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right">

        <!-- APPLICATIONS -->
        <div class="card">
            <h3>Positions Applied For</h3>
            <table>
                <tr>
                    <th>Position</th>
                    <th>Department</th>
                    <th>Status</th>
                </tr>
                <?php if(empty($applications)): ?>
                    <tr><td colspan="3">No applications yet.</td></tr>
                <?php else: ?>
                    <?php foreach($applications as $app): ?>
                        <tr>
                            <td><?= esc($app['position']) ?></td>
                            <td><?= esc($app['department']) ?></td>
                            <td>
                                <span class="status <?= esc($app['status']) ?>">
                                    <?= esc($app['status']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>

        <!-- VACANCIES -->
        <div class="card">
            <h3>Available Job Vacancies</h3>
            <table>
                <tr>
                    <th>Position</th>
                    <th>Department</th>
                </tr>
                <?php if(empty($vacancies)): ?>
                    <tr><td colspan="2">No open vacancies.</td></tr>
                <?php else: ?>
                    <?php foreach($vacancies as $vac): ?>
                        <tr>
                            <td><?= esc($vac['position']) ?></td>
                            <td><?= esc($vac['department']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>

    </div>

</div>

</body>
</html>
