
<body>
    <h1>Employee Permanently Deleted</h1>
    <p>First Name: {{ $employee->first_name }}</p>
    <p>Last Name: {{ $employee->last_name }}</p>
    <p>Email: {{ $employee->email }}</p>
    <p>Phone: {{ $employee->phone }}</p>
    <p>Company: {{ $employee->company->name ?? 'No Company Assigned' }}</p>
</body>
</html>
